<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use App\Models\Questionnaire;
use App\Models\Question;
use App\Models\Response;
use App\Models\Suggestion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class QuestionnaireImportController extends Controller
{
    public function index()
    {
        return view('admin.import.index');
    }

    public function import(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        try {
            $request->validate([
                'excel_file' => 'required|file|max:10240',
                'questionnaire_id' => 'required|exists:questionnaires,id'
            ]);

            $file = $request->file('excel_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $mimeType = $file->getMimeType();

            $allowedExtensions = ['xlsx', 'xls', 'csv'];
            $allowedMimeTypes = [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel',
                'text/csv',
                'text/plain',
                'application/csv'
            ];

            if (!in_array($extension, $allowedExtensions) && !in_array($mimeType, $allowedMimeTypes)) {
                throw new \Exception("Format file tidak didukung. Gunakan file .xlsx, .xls, atau .csv.");
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Validasi gagal: ' . $e->getMessage());
        }

        try {
            DB::beginTransaction();

            $questionnaire = Questionnaire::findOrFail($request->questionnaire_id);
            $questions = $questionnaire->questions()->orderBy('order')->get();

            $data = Excel::toArray([], $request->file('excel_file'));

            if (empty($data) || empty($data[0])) {
                throw new \Exception('File Excel/CSV kosong atau tidak dapat dibaca');
            }

            $rows = $data[0];

            if (count($rows) < 2) {
                throw new \Exception('File CSV harus memiliki minimal 2 baris (header + 1 data)');
            }

            $header = array_shift($rows);
            $expectedColumns = $this->getExpectedColumns($questionnaire->type, $questions->count());
            $actualColumns = count($header);

            if ($actualColumns < $expectedColumns) {
                $message = "Jumlah kolom tidak sesuai untuk tipe '{$questionnaire->type}'. Dibutuhkan {$expectedColumns} kolom, tetapi file hanya memiliki {$actualColumns} kolom.";
                return back()->with('error', $message);
            }

            // Tambahkan di method import() setelah validasi header
            // Letakkan setelah baris: if ($actualColumns < $expectedColumns) { ... }

            \Log::info('=== DEBUG IMPORT START ===');
            \Log::info('Questionnaire Type: ' . $questionnaire->type);
            \Log::info('Expected Columns: ' . $expectedColumns);
            \Log::info('Actual Columns: ' . $actualColumns);
            \Log::info('Header: ' . json_encode($header));

            $imported = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                try {
                    \Log::info("Processing Row " . ($index + 2) . ": " . json_encode($row));

                    if (empty(array_filter($row))) {
                        \Log::info("Row " . ($index + 2) . " is empty, skipping");
                        continue;
                    }

                    $userData = $this->processRowByType($questionnaire->type, $row, $index);
                    \Log::info("User Data Row " . ($index + 2) . ": " . json_encode($userData));

                    if (!$userData) {
                        \Log::info("Row " . ($index + 2) . " userData is null, skipping");
                        continue;
                    }

                    $user = $this->findExistingUser($userData);
                    \Log::info("User found/created for Row " . ($index + 2) . ": " . ($user ? $user->id : 'NEW'));

                    if (!$user) {
                        $userData['username'] = $this->generateUniqueUsername($userData['email']);
                        $userData['password'] = bcrypt('password');
                        $userData['is_active'] = true;
                        $user = User::create($userData);
                        \Log::info("New User Created Row " . ($index + 2) . ": " . $user->id);
                    }

                    $responseStartColumn = $this->getResponseStartColumn($questionnaire->type);
                    \Log::info("Response Start Column for Row " . ($index + 2) . ": " . $responseStartColumn);

                    $this->processResponses($questionnaire, $questions, $user, $row, $responseStartColumn);
                    \Log::info("Responses processed for Row " . ($index + 2));

                    // Processing suggestions
                    if ($questionnaire->type === 'kepuasan_mitra') {
                        $this->processMitraSuggestions($questionnaire, $user, $row, $questions->count());
                        \Log::info("Mitra suggestions processed for Row " . ($index + 2));
                    } elseif ($questionnaire->type === 'kepuasan_pengguna_lulusan') {
                        $this->processPenggunaLulusanSuggestions($questionnaire, $user, $row, $questions->count());
                        \Log::info("Pengguna Lulusan suggestions processed for Row " . ($index + 2));
                    }

                    if (!$questionnaire->users()->where('user_id', $user->id)->exists()) {
                        $questionnaire->users()->attach($user->id, [
                            'submitted_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        \Log::info("User attached to questionnaire for Row " . ($index + 2));
                    }

                    $imported++;
                    \Log::info("Row " . ($index + 2) . " imported successfully");
                } catch (\Exception $e) {
                    $error = "Baris " . ($index + 2) . ": " . $e->getMessage();
                    $errors[] = $error;

                    // LOG ERROR DETAIL
                    \Log::error('=== IMPORT ERROR ===');
                    \Log::error('Row: ' . ($index + 2));
                    \Log::error('Data: ' . json_encode($row));
                    \Log::error('Error Message: ' . $e->getMessage());
                    \Log::error('Error File: ' . $e->getFile());
                    \Log::error('Error Line: ' . $e->getLine());
                    \Log::error('Stack Trace: ' . $e->getTraceAsString());
                    \Log::error('=== END ERROR ===');
                }
            }

            \Log::info('=== DEBUG IMPORT END ===');
            \Log::info('Total Imported: ' . $imported);
            \Log::info('Total Errors: ' . count($errors));
            if (!empty($errors)) {
                \Log::info('Error List: ' . json_encode($errors));
            }

            $imported = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                try {
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    $userData = $this->processRowByType($questionnaire->type, $row, $index);

                    if (!$userData) {
                        continue;
                    }

                    $user = $this->findExistingUser($userData);

                    if (!$user) {
                        $userData['username'] = $this->generateUniqueUsername($userData['email']);
                        $userData['password'] = bcrypt('password');
                        $userData['is_active'] = true;

                        $user = User::create($userData);
                    }

                    $responseStartColumn = $this->getResponseStartColumn($questionnaire->type);
                    $this->processResponses($questionnaire, $questions, $user, $row, $responseStartColumn);

                    // Perbaikan untuk processing suggestions berdasarkan tipe
                    if ($questionnaire->type === 'kepuasan_mitra') {
                        $this->processMitraSuggestions($questionnaire, $user, $row, $questions->count());
                    } elseif ($questionnaire->type === 'kepuasan_pengguna_lulusan') {
                        $this->processPenggunaLulusanSuggestions($questionnaire, $user, $row, $questions->count());
                    }

                    if (!$questionnaire->users()->where('user_id', $user->id)->exists()) {
                        $questionnaire->users()->attach($user->id, [
                            'submitted_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }

                    $imported++;
                } catch (\Exception $e) {
                    $error = "Baris " . ($index + 2) . ": " . $e->getMessage();
                    $errors[] = $error;
                }
            }

            DB::commit();

            $message = "Import berhasil! {$imported} responden diimport.";

            // Perbaikan: Kirim errors sebagai session flash data, bukan dengan()
            if (!empty($errors)) {
                $message .= " Dengan " . count($errors) . " error.";
                session()->flash('import_errors', $errors);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    private function processPenggunaLulusanSuggestions($questionnaire, $user, $row, $questionsCount)
    {
        $harapanColumn = 8 + $questionsCount;        // Kolom setelah pertanyaan terakhir
        $saranColumn = 8 + $questionsCount + 1;     // Kolom berikutnya

        $harapan = $row[$harapanColumn] ?? null;
        $saran = $row[$saranColumn] ?? null;

        if (!empty($harapan)) {
            Suggestion::create([
                'user_id' => $user->id,
                'questionnaire_id' => $questionnaire->id,
                'content' => 'Harapan : ' . $harapan,
                'status' => 'submitted'
            ]);
        }

        if (!empty($saran)) {
            Suggestion::create([
                'user_id' => $user->id,
                'questionnaire_id' => $questionnaire->id,
                'content' => 'Saran : ' . $saran,
                'status' => 'submitted'
            ]);
        }
    }

    private function getExpectedColumns($questionnaireType, $questionsCount)
    {
        switch ($questionnaireType) {
            case 'kepuasan_mitra':
                return 10 + $questionsCount + 2; // 10 data mitra + questions + 2 saran
            case 'kepuasan_pengguna_lulusan':
                return 8 + $questionsCount + 2; // 8 data + questions + 2 saran (harapan & saran)
            case 'kepuasan_alumni':
                return 8 + $questionsCount; // 8 data standar + questions
            default:
                return 8 + $questionsCount; // 8 data standar + questions
        }
    }

    private function processRowByType($questionnaireType, $row, $index)
    {
        switch ($questionnaireType) {
            case 'kepuasan_mitra':
                return $this->processMitraRow($row, $index);
            case 'kepuasan_alumni':
                return $this->processAlumniRow($row, $index);
            default:
                return $this->processStandardRow($row, $index, $questionnaireType);
        }
    }

    private function processMitraRow($row, $index)
    {
        $userName = $row[0] ?? null;
        $userEmail = $row[1] ?? null;
        $namaInstansi = $row[2] ?? null;
        $jabatan = $row[3] ?? null;
        $jenisMitra = $row[4] ?? null;
        $jenisKerjasama = $row[5] ?? null;
        $lingkupKerjasama = $row[6] ?? null;
        $periodeKerjasama = $row[7] ?? null;
        $noTelepon = $row[8] ?? null;
        $alamat = $row[9] ?? null;

        if (empty($userName)) {
            throw new \Exception("Nama responden kosong");
        }

        if (empty($userEmail) || !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Email tidak valid atau kosong");
        }

        $userData = [
            'name' => $userName,
            'email' => $userEmail,
            'role' => 'mitra',
            'nama_instansi' => $namaInstansi,
            'jabatan' => $jabatan,
            'meta_jenis_mitra' => $jenisMitra,
            'meta_jenis_kerjasama' => $jenisKerjasama,
            'meta_lingkup_kerjasama' => $lingkupKerjasama,
            'meta_periode_kerjasama' => $periodeKerjasama,
            'no_telepon' => $noTelepon,
            'meta_alamat' => $alamat,
        ];

        return $userData;
    }

    private function processAlumniRow($row, $index)
    {
        $nim = $row[0] ?? null;
        $nip = $row[1] ?? null;
        $nik = $row[2] ?? null;
        $userName = $row[3] ?? null;
        $userEmail = $row[4] ?? null;
        $programStudi = $row[5] ?? null;
        $noTelepon = $row[6] ?? null;
        $fieldTambahan = $row[7] ?? null;

        if (empty($userName)) {
            throw new \Exception("Nama responden kosong");
        }

        if (empty($userEmail) || !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Email tidak valid atau kosong");
        }

        $userData = [
            'name' => $userName,
            'email' => $userEmail,
            'role' => 'alumni',
            'program_studi' => $programStudi,
            'no_telepon' => $noTelepon,
        ];

        if (!empty($nim)) $userData['nim'] = $nim;
        if (!empty($nip)) $userData['nip'] = $nip;
        if (!empty($nik)) $userData['nik'] = $nik;

        if ($fieldTambahan) {
            $alumniData = explode('|', $fieldTambahan);
            $userData['tahun_lulus'] = $alumniData[0] ?? null;
            $userData['domisili'] = $alumniData[1] ?? null;
            $userData['npwp'] = $alumniData[2] ?? null;
        }

        return $userData;
    }

    private function processPenggunaLulusanRow($row, $index)
    {
        $userName = $row[0] ?? null;
        $userEmail = $row[1] ?? null;
        $namaPerusahaan = $row[2] ?? null;
        $jabatan = $row[3] ?? null;
        $namaAlumni = $row[4] ?? null;
        $tahunLulusAlumni = $row[5] ?? null;
        $programStudiAlumni = $row[6] ?? null;
        $noTelepon = $row[7] ?? null;

        if (empty($userName)) {
            throw new \Exception("Nama responden kosong");
        }

        if (empty($userEmail) || !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Email tidak valid atau kosong");
        }

        return [
            'name' => $userName,
            'email' => $userEmail,
            'role' => 'pengguna_lulusan',
            'nama_instansi' => $namaPerusahaan,
            'jabatan' => $jabatan,
            'meta_nama_alumni' => $namaAlumni,
            'meta_tahun_lulus_alumni' => $tahunLulusAlumni,
            'meta_program_studi_alumni' => $programStudiAlumni,
            'no_telepon' => $noTelepon,
        ];
    }

    private function processStandardRow($row, $index, $questionnaireType)
    {
        if ($questionnaireType === 'kepuasan_pengguna_lulusan') {
            return $this->processPenggunaLulusanRow($row, $index);
        }

        // Standard processing untuk tipe lain
        $nim = $row[0] ?? null;
        $nip = $row[1] ?? null;
        $nik = $row[2] ?? null;
        $userName = $row[3] ?? null;
        $userEmail = $row[4] ?? null;
        $programStudi = $row[5] ?? null;
        $noTelepon = $row[6] ?? null;
        $fieldTambahan = $row[7] ?? null;

        if (empty($userName)) {
            throw new \Exception("Nama responden kosong");
        }

        if (empty($userEmail) || !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Email tidak valid atau kosong");
        }

        $role = $this->determineRole($questionnaireType);

        $userData = [
            'name' => $userName,
            'email' => $userEmail,
            'role' => $role,
            'no_telepon' => $noTelepon,
        ];

        if (!empty($nim)) $userData['nim'] = $nim;
        if (!empty($nip)) $userData['nip'] = $nip;
        if (!empty($nik)) $userData['nik'] = $nik;

        if (in_array($role, ['mahasiswa', 'dosen', 'alumni'])) {
            $userData['program_studi'] = $programStudi;
        }

        return $userData;
    }

    private function getResponseStartColumn($questionnaireType)
    {
        switch ($questionnaireType) {
            case 'kepuasan_mitra':
                return 10; // Setelah alamat (kolom J), karena sekarang ada 10 kolom
            case 'kepuasan_pengguna_lulusan':
                return 8; // Setelah no_telepon (kolom H)
            default:
                return 8; // Setelah field tambahan standar
        }
    }

    private function processResponses($questionnaire, $questions, $user, $row, $responseStartColumn)
    {
        foreach ($questions as $questionIndex => $question) {
            $columnIndex = $responseStartColumn + $questionIndex;

            if (!isset($row[$columnIndex])) {
                continue;
            }

            $originalValue = $row[$columnIndex];
            $rating = $this->convertRatingToNumber($originalValue);

            if ($rating >= 1 && $rating <= 4) {
                $existingResponse = Response::where([
                    'questionnaire_id' => $questionnaire->id,
                    'question_id' => $question->id,
                    'user_id' => $user->id
                ])->first();

                if (!$existingResponse) {
                    Response::create([
                        'questionnaire_id' => $questionnaire->id,
                        'question_id' => $question->id,
                        'user_id' => $user->id,
                        'rating' => $rating,
                        'comment' => null
                    ]);
                }
            }
        }
    }

    private function processMitraSuggestions($questionnaire, $user, $row, $questionsCount)
    {
        $saranMitraColumn = 10 + $questionsCount;        // Kolom setelah pertanyaan terakhir (sekarang dimulai dari kolom 10)
        $saranKemajuanColumn = 10 + $questionsCount + 1; // Kolom berikutnya

        $saranMitra = $row[$saranMitraColumn] ?? null;
        $saranKemajuan = $row[$saranKemajuanColumn] ?? null;

        if (!empty($saranMitra)) {
            Suggestion::create([
                'user_id' => $user->id,
                'questionnaire_id' => $questionnaire->id,
                'content' => 'Saran dari Mitra: ' . $saranMitra,
                'status' => 'submitted'
            ]);
        }

        if (!empty($saranKemajuan)) {
            Suggestion::create([
                'user_id' => $user->id,
                'questionnaire_id' => $questionnaire->id,
                'content' => 'Saran Kemajuan FIK: ' . $saranKemajuan,
                'status' => 'submitted'
            ]);
        }
    }

    private function findExistingUser($userData)
    {
        $query = User::where('email', $userData['email']);

        if (!empty($userData['nim'])) {
            $query->orWhere('nim', $userData['nim']);
        }
        if (!empty($userData['nip'])) {
            $query->orWhere('nip', $userData['nip']);
        }
        if (!empty($userData['nik'])) {
            $query->orWhere('nik', $userData['nik']);
        }

        return $query->first();
    }

    private function generateUniqueUsername($email)
    {
        $timestamp = now()->format('YmdHis');
        $emailPart = substr(explode('@', $email)[0], 0, 5);
        $username = $emailPart . '_' . $timestamp . '_' . rand(100, 999);

        while (User::where('username', $username)->exists()) {
            $username = $emailPart . '_' . $timestamp . '_' . rand(1000, 9999);
        }

        return $username;
    }

    private function determineRole($questionnaireType)
    {
        $roleMapping = [
            'evaluasi_dosen' => 'mahasiswa',
            'elom' => 'mahasiswa',
            'elta' => 'mahasiswa',
            'kepuasan_dosen' => 'dosen',
            'kepuasan_tendik' => 'tendik',
            'kepuasan_alumni' => 'alumni',
            'kepuasan_pengguna_lulusan' => 'pengguna_lulusan',
            'kepuasan_mitra' => 'mitra',
            'layanan_fakultas' => 'mahasiswa'
        ];

        return $roleMapping[$questionnaireType] ?? 'mahasiswa';
    }

    private function convertRatingToNumber($value)
    {
        $value = strtolower(trim($value));

        if (is_numeric($value)) {
            $num = (int) $value;
            if ($num >= 1 && $num <= 4) return $num;
            if ($num == 5) return 4;
        }

        $mapping = [
            'sangat kurang' => 1,
            'kurang' => 2,
            'baik' => 3,
            'sangat baik' => 4,
            'sangat tidak puas' => 1,
            'tidak puas' => 2,
            'puas' => 3,
            'sangat puas' => 4,
            'tidak setuju' => 1,
            'kurang setuju' => 2,
            'setuju' => 3,
            'sangat setuju' => 4
        ];

        return $mapping[$value] ?? 3;
    }

    public function checkLog()
    {
        $logFile = storage_path('logs/laravel.log');

        if (!file_exists($logFile)) {
            return response('<h2>Log file tidak ditemukan</h2>');
        }

        $file = new \SplFileObject($logFile);
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key();
        $startLine = max(0, $totalLines - 200);
        $file->seek($startLine);

        $lines = [];
        while (!$file->eof()) {
            $line = $file->fgets();
            if (stripos($line, 'import') !== false || stripos($line, 'error') !== false) {
                $lines[] = htmlspecialchars($line);
            }
        }

        $output = '<h2>Log Import Terbaru</h2>';
        $output .= '<div style="background: #000; color: #fff; padding: 10px; font-family: monospace; max-height: 500px; overflow-y: auto;">';
        $output .= implode('<br>', array_slice($lines, -50));
        $output .= '</div>';
        $output .= '<p><a href="javascript:location.reload()">Refresh</a> | <a href="javascript:window.close()">Tutup</a></p>';

        return response($output);
    }

    public function downloadTemplate(Request $request)
    {
        $questionnaireId = $request->get('questionnaire_id');

        if (!$questionnaireId) {
            return back()->with('error', 'Pilih kuesioner terlebih dahulu');
        }

        $questionnaire = Questionnaire::findOrFail($questionnaireId);
        $questions = $questionnaire->questions()->orderBy('order')->get();

        $headers = $this->getHeadersByType($questionnaire->type, $questions);
        $sampleData = $this->getSampleDataByType($questionnaire->type, $questions->count());

        $csvData = array_merge([$headers], $sampleData);

        $csvContent = '';
        foreach ($csvData as $row) {
            $csvContent .= implode(',', array_map(function ($field) {
                if (strpos($field, ',') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false) {
                    return '"' . str_replace('"', '""', $field) . '"';
                }
                return $field;
            }, $row)) . "\n";
        }

        $filename = 'template_import_' . $questionnaire->slug . '_' . date('Y-m-d') . '.csv';

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    private function getHeadersByType($questionnaireType, $questions)
    {
        switch ($questionnaireType) {
            case 'kepuasan_mitra':
                $headers = ['Nama Responden', 'Email', 'Nama Institusi', 'Jabatan', 'Jenis Mitra', 'Jenis Kerjasama', 'Lingkup Kerjasama', 'Periode Kerjasama', 'No Telepon', 'Alamat'];
                break;
            case 'kepuasan_pengguna_lulusan':
                $headers = ['Nama Responden', 'Email', 'Nama Perusahaan/Institusi', 'Jabatan', 'Nama Alumni FIK', 'Tahun Lulus Alumni', 'Program Studi Alumni', 'No Telepon'];
                break;
            default:
                $headers = ['NIM', 'NIP', 'NIK', 'Nama Responden', 'Email', 'Program Studi', 'No Telepon', 'Field Tambahan'];
                break;
        }

        foreach ($questions as $index => $question) {
            $shortQuestion = substr(strip_tags($question->question), 0, 40);
            $headers[] = 'P' . ($index + 1) . ' - ' . $shortQuestion . (strlen($question->question) > 40 ? '...' : '');
        }

        if ($questionnaireType === 'kepuasan_mitra') {
            $headers[] = 'Saran dari Mitra';
            $headers[] = 'Saran Kemajuan FIK';
        } elseif ($questionnaireType === 'kepuasan_pengguna_lulusan') {
            $headers[] = 'Harapan untuk alumni FIK UPNVJ';
            $headers[] = 'Saran dan masukan untuk FIK UPNVJ';
        }

        return $headers;
    }

    private function getSampleDataByType($questionnaireType, $questionsCount)
    {
        switch ($questionnaireType) {
            case 'kepuasan_mitra':
                return [
                    array_merge([
                        'Ahmad Partner',
                        'ahmad.partner@company.com',
                        'PT ABC Technology',
                        'Partnership Manager',
                        'Industri',
                        'Pendidikan & Pelatihan',
                        'Magang & PKL',
                        '2024-2025',
                        '081234567890',
                        'Jakarta Selatan'
                    ], array_fill(0, $questionsCount, rand(3, 4)), [
                        'Pelayanan sangat baik dan profesional',
                        'Terus tingkatkan kualitas lulusan'
                    ])
                ];
            case 'kepuasan_pengguna_lulusan':
                return [
                    array_merge([
                        'Ahmad Manager',
                        'ahmad.manager@company.com',
                        'PT XYZ Technology',
                        'HR Manager',
                        'Siti Alumna',
                        '2020',
                        'S1 Informatika',
                        '081234567890'
                    ], array_fill(0, $questionsCount, rand(3, 4)), [
                        'Alumni sangat kompeten dan profesional',
                        'Terus tingkatkan kualitas lulusan'
                    ])
                ];
            default:
                return [];
        }
    }

    public function downloadResults(Request $request)
    {
        $questionnaireId = $request->get('questionnaire_id');

        if (!$questionnaireId) {
            return back()->with('error', 'Pilih kuesioner terlebih dahulu');
        }

        try {
            $questionnaire = Questionnaire::findOrFail($questionnaireId);
            $questions = $questionnaire->questions()->orderBy('order')->get();

            $headers = $this->getHeadersByType($questionnaire->type, $questions);

            $respondents = DB::table('questionnaire_user')
                ->join('users', 'questionnaire_user.user_id', '=', 'users.id')
                ->where('questionnaire_user.questionnaire_id', $questionnaireId)
                ->whereNotNull('questionnaire_user.submitted_at')
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.nim',
                    'users.nip',
                    'users.nik',
                    'users.program_studi',
                    'users.no_telepon',
                    'users.tahun_lulus',
                    'users.domisili',
                    'users.npwp',
                    'users.nama_instansi',
                    'users.jabatan',
                    'users.meta_jenis_mitra',
                    'users.meta_jenis_kerjasama',
                    'users.meta_lingkup_kerjasama',
                    'users.meta_periode_kerjasama',
                    'users.meta_alamat',
                    'users.meta_nama_alumni',
                    'users.meta_tahun_lulus_alumni',
                    'users.meta_program_studi_alumni',
                    'users.role'
                )
                ->get();

            $csvData = [$headers];

            foreach ($respondents as $respondent) {
                $rowData = $this->buildRowDataByType($questionnaire->type, $respondent, $questionnaire, $questions);
                $csvData[] = $rowData;
            }

            // Jika tidak ada data existing, buat template kosong
            if ($respondents->isEmpty()) {
                $sampleData = $this->getSampleDataByType($questionnaire->type, $questions->count());
                if (!empty($sampleData)) {
                    $csvData = array_merge($csvData, $sampleData);
                }
            }

            $csvContent = '';
            foreach ($csvData as $row) {
                $csvContent .= implode(';', array_map(function ($field) {
                    if (strpos($field, ';') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false) {
                        return '"' . str_replace('"', '""', $field) . '"';
                    }
                    return $field;
                }, $row)) . "\n";
            }

            $filename = 'hasil_kuesioner_' . $questionnaire->slug . '_' . date('Y-m-d_H-i-s') . '.csv';

            return response($csvContent, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Export gagal: ' . $e->getMessage());
        }
    }

    private function buildRowDataByType($questionnaireType, $respondent, $questionnaire, $questions)
    {
        switch ($questionnaireType) {
            case 'kepuasan_mitra':
                $rowData = [
                    $respondent->name,
                    $respondent->email,
                    $respondent->nama_instansi ?? '-',
                    $respondent->jabatan ?? '-',
                    $respondent->meta_jenis_mitra ?? '-',
                    $respondent->meta_jenis_kerjasama ?? '-',
                    $respondent->meta_lingkup_kerjasama ?? '-',
                    $respondent->meta_periode_kerjasama ?? '-',
                    $respondent->no_telepon ?? '-',
                    $respondent->meta_alamat ?? '-'
                ];
                break;
            case 'kepuasan_pengguna_lulusan':
                $rowData = [
                    $respondent->name,
                    $respondent->email,
                    $respondent->nama_instansi ?? '-',
                    $respondent->jabatan ?? '-',
                    $respondent->meta_nama_alumni ?? '-',
                    $respondent->meta_tahun_lulus_alumni ?? '-',
                    $respondent->meta_program_studi_alumni ?? '-',
                    $respondent->no_telepon ?? '-'
                ];
                break;
            default:
                $fieldTambahan = '-';
                if ($respondent->role === 'alumni') {
                    $fieldTambahan = implode('|', array_filter([
                        $respondent->tahun_lulus,
                        $respondent->domisili,
                        $respondent->npwp
                    ]));
                }

                $rowData = [
                    $respondent->nim ?? '',
                    $respondent->nip ?? '',
                    $respondent->nik ?? '',
                    $respondent->name,
                    $respondent->email,
                    $respondent->program_studi ?? '-',
                    $respondent->no_telepon ?? '-',
                    $fieldTambahan ?: '-'
                ];
                break;
        }

        // Add responses
        foreach ($questions as $question) {
            $response = Response::where([
                'questionnaire_id' => $questionnaire->id,
                'question_id' => $question->id,
                'user_id' => $respondent->id
            ])->first();

            $rowData[] = $response ? $response->rating : '';
        }

        // Add suggestions
        if ($questionnaireType === 'kepuasan_mitra') {
            $suggestions = Suggestion::where([
                'questionnaire_id' => $questionnaire->id,
                'user_id' => $respondent->id
            ])->get();

            $saranMitra = '';
            $saranKemajuan = '';

            foreach ($suggestions as $suggestion) {
                if (str_contains($suggestion->content, 'Saran dari Mitra:')) {
                    $saranMitra = str_replace('Saran dari Mitra: ', '', $suggestion->content);
                } elseif (str_contains($suggestion->content, 'Saran Kemajuan FIK:')) {
                    $saranKemajuan = str_replace('Saran Kemajuan FIK: ', '', $suggestion->content);
                }
            }

            $rowData[] = $saranMitra;
            $rowData[] = $saranKemajuan;
        } elseif ($questionnaireType === 'kepuasan_pengguna_lulusan') {
            // $suggestion = Suggestion::where([
            //     'questionnaire_id' => $questionnaire->id,
            //     'user_id' => $respondent->id
            // ])->first();

            // $rowData[] = $suggestion ? $suggestion->content : '';
            $suggestions = Suggestion::where([
                'questionnaire_id' => $questionnaire->id,
                'user_id' => $respondent->id
            ])->get();

            $harapan = '';
            $saran = '';

            foreach ($suggestions as $suggestion) {
                if (str_contains($suggestion->content, 'Harapan :')) {
                    $harapan = str_replace('Harapan : ', '', $suggestion->content);
                } elseif (str_contains($suggestion->content, 'Saran :')) {
                    $saran = str_replace('Saran : ', '', $suggestion->content);
                }
            }

            $rowData[] = $harapan;
            $rowData[] = $saran;
        }

        return $rowData;
    }
}
