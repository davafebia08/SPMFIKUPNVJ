<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@upnvj.ac.id',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now()
        ]);

        // Dekan/Pimpinan
        User::create([
            'name' => 'Dekan FIK',
            'email' => 'dekan@upnvj.ac.id',
            'username' => 'dekan',
            'password' => Hash::make('password'),
            'role' => 'pimpinan',
            'email_verified_at' => now()
        ]);

        // Tendik (data baru)
        $tendikData = [
            [
                'name' => 'Saimin, S.Kom',
                'nip' => '196811112021211003',
                'email' => 'saimin.fik@upnvj.ac.id',
            ],
            [
                'name' => 'Fitriadi, S.Kom',
                'nip' => '198706012021211001',
                'email' => 'f.adhy92@unpnvj.ac.id',
            ],
            [
                'name' => 'Suryadi, A.Md',
                'nip' => '197507012021211004',
                'email' => 'suryadi.fikupnvj@ac.id',
            ],
            [
                'name' => 'Fitriyah Ningsih, S.Kom',
                'nip' => '490041310241',
                'email' => 'fitriyahningsih@upnvj.ac.id',
            ],
            [
                'name' => 'Sugiyanto',
                'nip' => '196902092021211002',
                'email' => 'Sugiyanto.fk@upnvj.ac.id',
            ],
            [
                'name' => 'Ayi Maulana',
                'nip' => '480051109451',
                'email' => 'Maulana@Upnvj.ac.id',
            ],
            [
                'name' => 'Zaenuddin',
                'nip' => '470039404121',
                'email' => 'jamalzainudin@upnvj.ac.id',
            ],
            [
                'name' => 'Roekan',
                'nip' => '473119404301',
                'email' => 'roekan14@gmail.com',
            ],
            [
                'name' => 'Astrianto Afandi, A.Md.Kom',
                'nip' => '2181985310003',
                'email' => 'astrianto.afandi@upnvj.ac.id',
            ],
            [
                'name' => 'Mochammad Fariz Satyawan, S.Kom',
                'nip' => '2181994310005',
                'email' => 'satyawanfariz@upnvj.ac.id',
            ],
            [
                'name' => 'Ika Marbela Sari, S.Kom',
                'nip' => '486010908731',
                'email' => 'ikamarbela.s@upnvj.ac.id',
            ],
            [
                'name' => 'Didit Surya Hartono, S.Kom',
                'nip' => '2191992310001',
                'email' => 'didit.suryahartono@upnvj.ac.id',
            ],
            [
                'name' => 'Diyah Retnowati, S.Kom',
                'nip' => '2191997320001',
                'email' => 'diyah.retnowati@upnvj.ac.id',
            ],
            [
                'name' => 'Rayhan Athaya Noor Hidayat, S.Kom',
                'nip' => '2232001310001',
                'email' => 'rayhanhidayat@upnvj.ac.id',
            ],
            [
                'name' => 'Arief Widyanto, S.Kom',
                'nip' => '2231997310001',
                'email' => 'arief.widyanto@upnvj.ac.id',
            ],
            [
                'name' => 'Yulia Chaerunnisa, S.Ikom',
                'nip' => '2231997320001',
                'email' => 'yuliachaerunisa@upnvj.ac.id',
            ],
        ];

        // Membuat akun untuk setiap tendik
        foreach ($tendikData as $index => $tendik) {
            // Membuat username dari nama tendik (ambil kata pertama dan ubah ke lowercase)
            $nameParts = explode(' ', $tendik['name']);
            $firstName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nameParts[0]));
            $username = $firstName;

            // Tambahkan angka jika ada tendik dengan nama depan yang sama
            if (User::where('username', $username)->exists()) {
                $username = $firstName . ($index + 1);
            }

            User::create([
                'name' => $tendik['name'],
                'email' => $tendik['email'],
                'username' => $username,
                'password' => Hash::make('password'),
                'role' => 'tendik',
                'nik' => $tendik['nip'], // Menggunakan kolom NIP/NIK yang diberikan
                'email_verified_at' => now()
            ]);
        }

        // Daftar program studi yang tersedia
        $programStudiList = [
            'S1 Informatika',
            'S1 Sains Data',
            'S1 Sistem Informasi',
            'D3 Sistem Informasi'
        ];

        // Mahasiswa (dengan program studi)
        for ($i = 1; $i <= 30; $i++) {
            // Ambil program studi berdasarkan indeks secara bergantian
            $programStudi = $programStudiList[($i - 1) % count($programStudiList)];

            // Tentukan angkatan (2020-2023)
            $angkatan = 2020 + (($i - 1) % 4);
            $nim = $angkatan . str_pad($i, 7, '0', STR_PAD_LEFT);

            User::create([
                'name' => "Mahasiswa $i",
                'email' => "mahasiswa$i@mahasiswa.upnvj.ac.id ",
                'username' => "mahasiswa$i",
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim' => $nim,
                'program_studi' => $programStudi,
                'email_verified_at' => now()
            ]);
        }

        // Mahasiswa Tugas Akhir (khusus untuk kuesioner ELTA)
        // Menggunakan meta data untuk menandai mahasiswa TA
        for ($i = 1; $i <= 10; $i++) {
            $programStudi = $programStudiList[($i - 1) % count($programStudiList)];

            // Mahasiswa TA biasanya angkatan 2020 atau 2021
            $angkatan = 2020 + ($i % 2);
            $nim = $angkatan . str_pad($i + 100, 7, '0', STR_PAD_LEFT);

            User::create([
                'name' => "Mahasiswa TA $i",
                'email' => "mahasiswa.ta$i@upnvj.ac.id",
                'username' => "mahasiswa.ta$i",
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim' => $nim,
                'program_studi' => $programStudi,
                'meta_alamat' => 'Mahasiswa Tugas Akhir', // Menggunakan meta_alamat sebagai flag untuk mahasiswa TA
                'email_verified_at' => now()
            ]);
        }

        // Alumni
        for ($i = 1; $i <= 10; $i++) {
            $programStudi = $programStudiList[($i - 1) % count($programStudiList)];
            $tahunLulus = 2020 + ($i % 5); // Rentang tahun 2020-2024

            // Generate NIK random 16 digit
            $nik = '3' . str_pad(rand(100000000000000, 999999999999999), 15, '0', STR_PAD_LEFT);

            // Generate NPWP random dengan format 00.000.000.0-000.000
            $npwp = sprintf(
                "%02d.%03d.%03d.%d-%03d.%03d",
                rand(0, 99),
                rand(0, 999),
                rand(0, 999),
                rand(0, 9),
                rand(0, 999),
                rand(0, 999)
            );

            // Array kota untuk domisili
            $domisili_array = ['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang', 'Medan', 'Makassar'];
            $domisili = $domisili_array[array_rand($domisili_array)];

            User::create([
                'name' => "Alumni $i",
                'email' => "alumni$i@gmail.com",
                'username' => "alumni$i",
                'password' => Hash::make('password'),
                'role' => 'alumni',
                'nim' => (2016 + ($i % 4)) . str_pad($i, 7, '0', STR_PAD_LEFT), // NIM saat jadi mahasiswa
                'tahun_lulus' => $tahunLulus,
                'program_studi' => $programStudi,
                'nik' => $nik,
                'npwp' => $npwp,
                'domisili' => $domisili,
                'email_verified_at' => now()
            ]);
        }

        // Pengguna Lulusan
        $perusahaan = [
            'PT Teknologi Maju Indonesia',
            'PT Global Data Solutions',
            'PT Karya Digital Nusantara',
            'PT Inovasi Teknologi Terkini',
            'Dinas Komunikasi dan Informatika DKI Jakarta',
            'Bank Mandiri Tbk',
            'Tokopedia'
        ];

        for ($i = 1; $i <= 7; $i++) {
            $namaPerusahaan = $perusahaan[$i - 1];
            $jabatan = ['HRD Manager', 'Direktur', 'Kepala Divisi IT', 'CEO', 'CTO'][$i % 5];

            User::create([
                'name' => "Pengguna Lulusan $i",
                'email' => "perusahaan$i@" . strtolower(str_replace(' ', '', substr($namaPerusahaan, 3, 6))) . ".com",
                'username' => "perusahaan$i",
                'password' => Hash::make('password'),
                'role' => 'pengguna_lulusan',
                'nama_instansi' => $namaPerusahaan,
                'jabatan' => $jabatan,
                'email_verified_at' => now()
            ]);
        }

        // Mitra Kerjasama
        $mitra = [
            'Universitas Indonesia',
            'Universitas Gadjah Mada',
            'PT Telkom Indonesia',
            'Kementerian Komunikasi dan Informatika',
            'PT Gojek Indonesia',
            'Microsoft Indonesia',
            'Google Indonesia'
        ];

        $jenisKerjasama = ['MoU', 'MoA', 'IA'];
        $jenisMitra = ['Perguruan Tinggi', 'Dunia Usaha/Dunia Industri', 'Pemerintah', 'TOP 100 CWU'];
        $lingkupKerjasama = ['Pendidikan', 'Penelitian', 'Pengabdian Masyarakat', 'Lainnya'];

        for ($i = 1; $i <= 7; $i++) {
            $namaMitra = $mitra[$i - 1];
            $jabatan = ['Direktur', 'Kepala Divisi Kerjasama', 'Rektor', 'Dekan', 'Manager Kerjasama'][$i % 5];

            User::create([
                'name' => "Mitra $i",
                'email' => "mitra$i@" . strtolower(str_replace(' ', '', substr($namaMitra, 0, 6))) . ".com",
                'username' => "mitra$i",
                'password' => Hash::make('password'),
                'role' => 'mitra',
                'nama_instansi' => $namaMitra,
                'jabatan' => $jabatan,
                'meta_jenis_kerjasama' => $jenisKerjasama[$i % 3],
                'meta_jenis_mitra' => $jenisMitra[$i % 4],
                'meta_lingkup_kerjasama' => $lingkupKerjasama[$i % 4],
                'meta_periode_kerjasama' => '2023-2025',
                'email_verified_at' => now()
            ]);
        }
    }
}
