<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\UpnvjApiService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DosenApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $apiService = new UpnvjApiService();

        // Menggunakan nilai dari environment Postman
        $periodId = env('API_PERIOD_ID', '20241');

        // Ambil data program studi dari API
        $this->command->info("Mengambil data program studi dari API...");
        $prodiResponse = $apiService->getStudyPrograms();

        if (!isset($prodiResponse['success']) || !$prodiResponse['success']) {
            $this->command->error("Gagal mengambil data program studi: " . ($prodiResponse['message'] ?? 'Unknown error'));

            // Gunakan data program studi default jika API gagal
            $programStudi = [
                ['id' => '1', 'name' => 'S1 Informatika'],
                ['id' => '2', 'name' => 'S1 Sistem Informasi'],
                ['id' => '3', 'name' => 'S1 Sains Data'],
                ['id' => '4', 'name' => 'D3 Sistem Informasi'],
            ];
        } else {
            // Dapatkan data program studi dari respons API
            $prodiData = null;
            if (isset($prodiResponse['data']['data'])) {
                $prodiData = $prodiResponse['data']['data'];
            } elseif (isset($prodiResponse['data'])) {
                $prodiData = $prodiResponse['data'];
            }

            if (empty($prodiData) || !is_array($prodiData)) {
                $this->command->warn("Tidak ada data program studi yang ditemukan, menggunakan data default");
                $programStudi = [
                    ['id' => '1', 'name' => 'S1 Informatika'],
                    ['id' => '2', 'name' => 'S1 Sistem Informasi'],
                    ['id' => '3', 'name' => 'S1 Sains Data'],
                    ['id' => '4', 'name' => 'D3 Sistem Informasi'],
                ];
            } else {
                // Tampilkan sampel data program studi
                $this->command->info("Contoh data program studi pertama: " . json_encode($prodiData[0], JSON_PRETTY_PRINT));

                // Filter program studi FIK
                $programStudi = [];
                foreach ($prodiData as $prodi) {
                    // Cari kunci yang berisi data ID dan nama program studi
                    $idKey = array_key_exists('id_program_studi', $prodi) ? 'id_program_studi' : (array_key_exists('kode_program_studi', $prodi) ? 'kode_program_studi' : 'id');
                    $nameKey = array_key_exists('nama_program_studi', $prodi) ? 'nama_program_studi' : (array_key_exists('nama', $prodi) ? 'nama' : 'name');

                    // Filter berdasarkan nama fakultas jika tersedia
                    $fakultasKey = array_key_exists('nama_fakultas', $prodi) ? 'nama_fakultas' : (array_key_exists('fakultas', $prodi) ? 'fakultas' : null);

                    // Filter program studi FIK
                    $isInformatics = false;
                    if ($fakultasKey && isset($prodi[$fakultasKey])) {
                        // Filter berdasarkan nama fakultas
                        $isInformatics = stripos($prodi[$fakultasKey], 'Ilmu Komputer') !== false ||
                            stripos($prodi[$fakultasKey], 'FIK') !== false;
                    } else {
                        // Filter berdasarkan nama program studi jika nama fakultas tidak tersedia
                        $isInformatics = stripos($prodi[$nameKey], 'Informatika') !== false ||
                            stripos($prodi[$nameKey], 'Sistem Informasi') !== false ||
                            stripos($prodi[$nameKey], 'Sains Data') !== false ||
                            stripos($prodi[$nameKey], 'Ilmu Komputer') !== false;
                    }

                    if ($isInformatics) {
                        $programStudi[] = [
                            'id' => $prodi[$idKey],
                            'name' => $prodi[$nameKey]
                        ];
                    }
                }

                // Jika tidak ada program studi FIK yang ditemukan, gunakan data default
                if (empty($programStudi)) {
                    $this->command->warn("Tidak ada program studi FIK yang ditemukan, menggunakan data default");
                    $programStudi = [
                        ['id' => '1', 'name' => 'S1 Informatika'],
                        ['id' => '2', 'name' => 'S1 Sistem Informasi'],
                        ['id' => '3', 'name' => 'S1 Sains Data'],
                        ['id' => '4', 'name' => 'D3 Sistem Informasi'],
                    ];
                } else {
                    $this->command->info("Berhasil mendapatkan " . count($programStudi) . " program studi dari API");
                    $this->command->line("Program studi yang ditemukan: " . json_encode($programStudi, JSON_PRETTY_PRINT));
                }
            }
        }

        // Hapus dosen yang ada jika diperlukan
        if ($this->command->confirm('Hapus semua data dosen yang ada sebelum impor?', true)) {
            $deleted = User::where('role', 'dosen')->delete();
            $this->command->info("Menghapus $deleted data dosen yang ada");
        }

        // Set untuk melacak ID dosen yang sudah ditambahkan untuk mencegah duplikasi
        $processedDosenIds = [];

        // Periksa skema tabel users untuk melihat kolom yang tersedia
        $hasMetaIdDosenColumn = Schema::hasColumn('users', 'meta_id_dosen');
        $hasMetaDataColumn = Schema::hasColumn('users', 'meta_data');

        // Sinkronkan dosen untuk setiap program studi
        foreach ($programStudi as $prodi) {
            $this->syncDosen($apiService, $periodId, $prodi, $processedDosenIds, $hasMetaIdDosenColumn, $hasMetaDataColumn);
        }
    }

    /**
     * Sinkronkan dosen untuk program studi tertentu
     */
    private function syncDosen($apiService, $periodId, $prodi, &$processedDosenIds, $hasMetaIdDosenColumn, $hasMetaDataColumn)
    {
        $this->command->info("Menyinkronkan data dosen untuk program studi {$prodi['name']}...");

        try {
            // Ambil data dosen dari API
            $response = $apiService->getLecturers($periodId, $prodi['id']);

            if (!isset($response['success']) || !$response['success']) {
                $this->command->error("Gagal mengambil data dosen untuk program studi {$prodi['name']}: " . ($response['message'] ?? 'Unknown error'));
                return;
            }

            // Dapatkan data dosen
            $dosenData = null;
            if (isset($response['data']['data'])) {
                $dosenData = $response['data']['data'];
            } elseif (isset($response['data'])) {
                $dosenData = $response['data'];
            }

            if (empty($dosenData) || !is_array($dosenData)) {
                $this->command->warn("Tidak ada data dosen yang ditemukan untuk program studi {$prodi['name']} atau format data tidak valid");
                return;
            }

            // Debugging: Tampilkan jumlah data dosen
            $this->command->info("Ditemukan " . count($dosenData) . " data dosen untuk program studi {$prodi['name']}");

            $syncCount = 0;
            $skipCount = 0;
            $duplicateCount = 0;

            foreach ($dosenData as $dosen) {
                // Periksa apakah data memiliki informasi yang diperlukan
                if (!isset($dosen['id_dosen']) || !isset($dosen['nama_dosen'])) {
                    $this->command->line("Melewati dosen karena data tidak lengkap");
                    $skipCount++;
                    continue;
                }

                $idDosen = $dosen['id_dosen'];
                $nama = $dosen['nama_dosen'];
                $nidn = $dosen['nidn_dosen'] ?? '';
                $ikatanKerja = $dosen['ikatan_kerja_dosen'] ?? '';
                $jabatanAkademik = $dosen['jabatan_akademik_dosen'] ?? '';

                // Gunakan NIDN jika tersedia, jika tidak gunakan ID dosen
                $nip = !empty($nidn) ? $nidn : $idDosen;

                // Lewati jika dosen dengan ID ini sudah diproses sebelumnya
                if (in_array($idDosen, $processedDosenIds)) {
                    $this->command->line("Melewati dosen {$nama} karena sudah diimpor sebelumnya");
                    $duplicateCount++;
                    continue;
                }

                // Ambil nama depan dari nama lengkap
                $nameParts = explode(' ', $nama);
                $firstName = strtolower($nameParts[0]);
                // Bersihkan nama dari karakter khusus
                $firstName = preg_replace('/[^a-z0-9]/', '', $firstName);

                // Buat username dari nama depan dan NIP
                $username = $firstName . '.' . $nip;

                // Buat email dari nama yang sudah dibersihkan
                $email = strtolower(str_replace(' ', '.', preg_replace('/[^a-zA-Z0-9\s]/', '', $nama))) . '@upnvj.ac.id';

                // Cek apakah dosen sudah ada di database (hanya berdasarkan nip dan username)
                $existingDosen = User::where('nip', $nip)
                    ->orWhere('username', $username)
                    ->first();

                $userData = [
                    'name' => $nama,
                    'email' => $email,
                    'username' => $username,
                    'password' => Hash::make('password'), // Default password
                    'role' => 'dosen',
                    'nip' => $nip, // NIDN atau ID dosen
                    'program_studi' => $prodi['name'],
                    'email_verified_at' => now(),
                    'jabatan' => $jabatanAkademik,
                ];

                // Tambahkan metadata berdasarkan kolom yang tersedia
                if ($hasMetaIdDosenColumn) {
                    $userData['meta_id_dosen'] = $idDosen;
                }

                if ($hasMetaDataColumn) {
                    $userData['meta_data'] = json_encode([
                        'id_dosen' => $idDosen,
                        'nidn' => $nidn,
                        'ikatan_kerja' => $ikatanKerja,
                        'jabatan_akademik' => $jabatanAkademik
                    ]);
                }

                // Gunakan kolom meta yang tersedia di tabel users
                // Jika ada kolom meta_alamat, gunakan untuk ikatan kerja
                if (Schema::hasColumn('users', 'meta_alamat')) {
                    $userData['meta_alamat'] = $ikatanKerja;
                }

                if ($existingDosen) {
                    // Update dosen yang sudah ada
                    $existingDosen->update($userData);
                    $this->command->line("Diperbarui: {$nama} (NIP: {$nip})");
                } else {
                    // Tambahkan dosen baru
                    User::create($userData);
                    $this->command->info("Ditambahkan: {$nama} (NIP: {$nip})");
                }

                // Tandai ID dosen ini sebagai sudah diproses
                $processedDosenIds[] = $idDosen;
                $syncCount++;
            }

            $this->command->info("Berhasil menyinkronkan {$syncCount} data dosen untuk program studi {$prodi['name']}");
            if ($skipCount > 0) {
                $this->command->warn("Melewati {$skipCount} data dosen karena tidak memenuhi kriteria");
            }
            if ($duplicateCount > 0) {
                $this->command->warn("Melewati {$duplicateCount} data dosen karena duplikat");
            }
        } catch (\Exception $e) {
            $this->command->error("Error saat menyinkronkan dosen untuk program studi {$prodi['name']}: {$e->getMessage()}");
        }
    }
}
