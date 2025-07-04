<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\Questionnaire;
use App\Models\Response;
use App\Models\User;
use App\Models\Suggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminResultController extends Controller
{
    public function index(Request $request)
    {
        // Mendapatkan semua periode akademik untuk dropdown filter
        $academicPeriods = AcademicPeriod::orderBy('year', 'desc')->orderBy('semester', 'desc')->get();

        // Mendapatkan nilai filter yang dipilih atau nilai default
        $selectedPeriodId = $request->input('academic_period_id', null);
        $selectedQuestionnaireType = $request->input('questionnaire_type', null);
        $selectedStatus = $request->input('status', null);
        $selectedProdi = $request->input('program_studi', null);

        // Mencari periode aktif jika tidak ada periode yang dipilih
        if (!$selectedPeriodId) {
            $activePeriod = AcademicPeriod::where('is_active', true)->first();
            $selectedPeriodId = $activePeriod ? $activePeriod->id : null;
        }

        // Query kuesioner berdasarkan filter
        $questionnairesQuery = Questionnaire::with('academicPeriod');

        if ($selectedPeriodId) {
            $questionnairesQuery->where('academic_period_id', $selectedPeriodId);
        }

        if ($selectedQuestionnaireType) {
            $questionnairesQuery->where('type', $selectedQuestionnaireType);
        }

        $questionnaires = $questionnairesQuery->get();

        // Menghitung jumlah responden untuk setiap kuesioner
        $questionnaireData = [];

        foreach ($questionnaires as $questionnaire) {
            // Menentukan status berdasarkan tanggal
            $now = now();
            $startDate = $questionnaire->start_date;
            $endDate = $questionnaire->end_date;

            if (!$questionnaire->is_active) {
                $status = 'non_active';
                $statusText = 'Non-Aktif';
            } elseif ($now->lt($startDate)) {
                $status = 'upcoming';
                $statusText = 'Akan Datang';
            } elseif ($now->gt($endDate)) {
                $status = 'completed';
                $statusText = 'Selesai';
            } else {
                $status = 'active';
                $statusText = 'Aktif';
            }

            // Lewati jika filter status diterapkan dan tidak cocok
            if ($selectedStatus && $status != $selectedStatus) {
                continue;
            }

            // Membuat query dasar untuk menghitung responden
            $respondentQuery = DB::table('questionnaire_user')
                ->where('questionnaire_id', $questionnaire->id)
                ->whereNotNull('submitted_at');

            // Filter berdasarkan program studi jika dipilih (kecuali untuk kuesioner mitra dan pengguna lulusan)
            if ($selectedProdi && !in_array($questionnaire->type, ['kepuasan_pengguna_lulusan', 'kepuasan_mitra'])) {
                $respondentQuery->join('users', 'questionnaire_user.user_id', '=', 'users.id')
                    ->where('users.program_studi', $selectedProdi);
            }

            $respondentCount = $respondentQuery->count();

            // Menentukan status evaluasi berdasarkan jumlah responden
            $evaluationStatus = $respondentCount > 0 ? 'Tersedia' : 'Belum Ada Data';

            $questionnaireData[] = [
                'questionnaire' => $questionnaire,
                'respondent_count' => $respondentCount,
                'status' => $status,
                'status_text' => $statusText,
                'evaluation_status' => $evaluationStatus
            ];
        }

        // Tipe kuesioner yang tersedia untuk filter
        $questionnaireTypes = [
            'layanan_fakultas' => 'Layanan Fakultas',
            'elom' => 'Evaluasi Layanan oleh Mahasiswa (ELOM)',
            'evaluasi_dosen' => 'Evaluasi Dosen oleh Mahasiswa',
            'elta' => 'Evaluasi Layanan Tugas Akhir (ELTA)',
            'kepuasan_dosen' => 'Kepuasan Dosen',
            'kepuasan_tendik' => 'Kepuasan Tenaga Kependidikan',
            'kepuasan_alumni' => 'Kepuasan Alumni',
            'kepuasan_pengguna_lulusan' => 'Kepuasan Pengguna Lulusan',
            'kepuasan_mitra' => 'Kepuasan Mitra Kerjasama'
        ];

        // Opsi status untuk filter
        $statusOptions = [
            'active' => 'Aktif',
            'completed' => 'Selesai',
            'upcoming' => 'Akan Datang',
            'non_active' => 'Non-Aktif'
        ];

        // Daftar program studi untuk filter
        $programStudiOptions = [
            'S1 Informatika' => 'S1 Informatika',
            'S1 Sistem Informasi' => 'S1 Sistem Informasi',
            'S1 Sains Data' => 'S1 Sains Data',
            'D3 Sistem Informasi' => 'D3 Sistem Informasi'
        ];

        return view('admin.results.index', compact(
            'academicPeriods',
            'selectedPeriodId',
            'selectedQuestionnaireType',
            'selectedStatus',
            'selectedProdi',
            'questionnaireData',
            'questionnaireTypes',
            'statusOptions',
            'programStudiOptions'
        ));
    }

    public function show(Questionnaire $questionnaire, Request $request)
    {
        $questionnaire->load('academicPeriod', 'questions.category');
        $selectedProdi = $request->input('program_studi', null);

        // Mendapatkan data respons dengan filter program studi jika diperlukan
        $responsesQuery = Response::where('questionnaire_id', $questionnaire->id);

        // Menerapkan filter program studi (kecuali untuk kuesioner mitra dan pengguna lulusan)
        if ($selectedProdi && !in_array($questionnaire->type, ['kepuasan_pengguna_lulusan', 'kepuasan_mitra'])) {
            $responsesQuery->whereHas('user', function ($query) use ($selectedProdi) {
                $query->where('program_studi', $selectedProdi);
            });
        }

        $responses = $responsesQuery->get();

        if ($responses->isEmpty()) {
            return redirect()->route('admin.results.index')
                ->with('info', 'Belum ada responden yang mengisi kuesioner ini.');
        }

        // Menghitung rata-rata rating per kategori
        $categoryRatings = $responses->groupBy('question.category.name')
            ->map(function ($items) {
                $sum = $items->sum('rating');
                $count = $items->count();

                return [
                    'average' => $count > 0 ? $sum / $count : 0,
                    'count' => $count
                ];
            });

        // Menghitung rata-rata rating per pertanyaan
        $questionRatings = $responses->groupBy('question_id')
            ->map(function ($items) {
                $sum = $items->sum('rating');
                $count = $items->count();

                return [
                    'average' => $count > 0 ? $sum / $count : 0,
                    'count' => $count,
                    'question' => $items->first()->question
                ];
            });

        // Menghitung rata-rata keseluruhan
        $overallAverage = $responses->average('rating');

        // Menghitung jumlah responden
        $respondentQuery = DB::table('questionnaire_user')
            ->where('questionnaire_id', $questionnaire->id)
            ->whereNotNull('submitted_at');

        // Menerapkan filter program studi jika dipilih (kecuali untuk kuesioner mitra dan pengguna lulusan)
        if ($selectedProdi && !in_array($questionnaire->type, ['kepuasan_pengguna_lulusan', 'kepuasan_mitra'])) {
            $respondentQuery->join('users', 'questionnaire_user.user_id', '=', 'users.id')
                ->where('users.program_studi', $selectedProdi);
        }

        $respondentCount = $respondentQuery->count();

        // Mendapatkan distribusi rating
        $ratingDistribution = $responses->groupBy('rating')
            ->map(function ($items) use ($responses) {
                $count = $items->count();
                $total = $responses->count();

                return [
                    'count' => $count,
                    'percentage' => $total > 0 ? ($count / $total) * 100 : 0
                ];
            });

        // Memastikan semua rating (1-4) terwakili
        for ($i = 1; $i <= 4; $i++) {
            if (!isset($ratingDistribution[$i])) {
                $ratingDistribution[$i] = [
                    'count' => 0,
                    'percentage' => 0
                ];
            }
        }

        // Mengurutkan berdasarkan nilai rating
        $ratingDistribution = $ratingDistribution->sortKeys();

        // Daftar program studi untuk filter
        $programStudiOptions = [
            'S1 Informatika' => 'S1 Informatika',
            'S1 Sistem Informasi' => 'S1 Sistem Informasi',
            'S1 Sains Data' => 'S1 Sains Data',
            'D3 Sistem Informasi' => 'D3 Sistem Informasi'
        ];

        return view('admin.results.show', compact(
            'questionnaire',
            'categoryRatings',
            'questionRatings',
            'overallAverage',
            'respondentCount',
            'ratingDistribution',
            'selectedProdi',
            'programStudiOptions'
        ));
    }

    public function respondents(Questionnaire $questionnaire, Request $request)
    {
        $questionnaire->load('academicPeriod');
        $selectedRole = $request->input('role', null);
        $searchQuery = $request->input('search', null);

        // Mendapatkan semua responden untuk kuesioner ini
        $respondentsQuery = DB::table('questionnaire_user')
            ->join('users', 'questionnaire_user.user_id', '=', 'users.id')
            ->where('questionnaire_user.questionnaire_id', $questionnaire->id)
            ->whereNotNull('questionnaire_user.submitted_at');

        // Menerapkan filter kategori user jika dipilih
        if ($selectedRole) {
            $respondentsQuery->where('users.role', $selectedRole);
        }

        // Menerapkan pencarian jika ada
        if ($searchQuery) {
            $respondentsQuery->where(function ($query) use ($searchQuery) {
                $query->where('users.name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('users.nim', 'like', '%' . $searchQuery . '%')
                    ->orWhere('users.nip', 'like', '%' . $searchQuery . '%')
                    ->orWhere('users.program_studi', 'like', '%' . $searchQuery . '%')
                    ->orWhere('users.nama_instansi', 'like', '%' . $searchQuery . '%');
            });
        }

        $respondents = $respondentsQuery->select(
            'users.id',
            'users.name',
            'users.role',
            'users.nim',
            'users.nip',
            'users.program_studi',
            'users.nama_instansi',
            'users.jabatan',
            'questionnaire_user.submitted_at'
        )
            ->orderBy('questionnaire_user.submitted_at', 'desc')
            ->paginate(10);

        // Daftar opsi kategori user untuk filter
        $roleOptions = [
            'mahasiswa' => 'Mahasiswa',
            'dosen' => 'Dosen',
            'tendik' => 'Tenaga Kependidikan',
            'alumni' => 'Alumni',
            'pengguna_lulusan' => 'Pengguna Lulusan',
            'mitra' => 'Mitra',
        ];

        return view('admin.results.respondents', compact(
            'questionnaire',
            'respondents',
            'selectedRole',
            'searchQuery',
            'roleOptions'
        ));
    }

    public function respondentDetail(Questionnaire $questionnaire, User $user)
    {
        $questionnaire->load('questions.category', 'academicPeriod');

        // Memeriksa apakah pengguna telah menyelesaikan kuesioner ini
        $submission = DB::table('questionnaire_user')
            ->where('questionnaire_id', $questionnaire->id)
            ->where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->first();

        if (!$submission) {
            return redirect()->route('admin.results.respondents', $questionnaire)
                ->with('error', 'Pengguna ini belum mengisi kuesioner.');
        }

        // Mendapatkan semua respons dari pengguna ini untuk kuesioner ini
        $responses = Response::where('questionnaire_id', $questionnaire->id)
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('question_id');

        // Mengelompokkan pertanyaan berdasarkan kategori
        $questionsByCategory = $questionnaire->questions->groupBy(function ($question) {
            return $question->category->name;
        });

        // Mendapatkan saran jika tersedia (untuk non-mitra)
        $suggestion = null;
        if ($user->role !== 'mitra') {
            $suggestion = Suggestion::where('questionnaire_id', $questionnaire->id)
                ->where('user_id', $user->id)
                ->first();
        }

        // Untuk mitra, saran akan diambil langsung di view menggunakan query terpisah
        // karena ada 2 jenis saran (saran mitra & saran kemajuan FIK)

        return view('admin.results.respondent-detail', compact(
            'questionnaire',
            'user',
            'responses',
            'questionsByCategory',
            'suggestion',
            'submission'
        ));
    }

    public function deleteResponse(Questionnaire $questionnaire, User $user)
    {
        // Menghapus semua respons dari pengguna ini untuk kuesioner ini
        Response::where('questionnaire_id', $questionnaire->id)
            ->where('user_id', $user->id)
            ->delete();

        // Menghapus saran jika ada (termasuk saran mitra)
        Suggestion::where('questionnaire_id', $questionnaire->id)
            ->where('user_id', $user->id)
            ->delete();

        // Menghapus record dari tabel pivot
        $questionnaire->users()->detach($user->id);

        return redirect()->route('admin.results.respondents', $questionnaire)
            ->with('success', 'Data responden berhasil dihapus.');
    }
}
