<?php

namespace Database\Seeders;

use App\Models\Questionnaire;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuestionnaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Periode aktif (2024/2025 Ganjil)
        $activePeriodId = 3;

        // Kuesioner Evaluasi Layanan Fakultas
        Questionnaire::create([
            'title' => 'Evaluasi Layanan Fakultas',
            'slug' => Str::slug('Evaluasi Layanan Fakultas'),
            'description' => 'Mengukur dan memahami tingkat kepuasan pengguna terhadap layanan di FIK UPNVJ',
            'type' => 'layanan_fakultas',
            'academic_period_id' => $activePeriodId,
            'start_date' => '2024-09-10',
            'end_date' => '2024-12-20',
            'is_active' => true,
        ]);

        // Kuesioner Evaluasi Layanan oleh Mahasiswa (ELOM)
        Questionnaire::create([
            'title' => 'Evaluasi Layanan oleh Mahasiswa (ELOM)',
            'slug' => Str::slug('Evaluasi Layanan oleh Mahasiswa (ELOM)'),
            'description' => 'Untuk menilai kinerja layanan yang diberikan FIK UPNVJ kepada Mahasiswa',
            'type' => 'elom',
            'academic_period_id' => $activePeriodId,
            'start_date' => '2024-09-10',
            'end_date' => '2024-12-20',
            'is_active' => true,
        ]);

        // Kuesioner Evaluasi Dosen oleh Mahasiswa
        Questionnaire::create([
            'title' => 'Evaluasi Dosen oleh Mahasiswa',
            'slug' => Str::slug('Evaluasi Dosen oleh Mahasiswa'),
            'description' => 'Meningkatkan kualitas pembelajaran dengan memastikan pengajaran memenuhi standar yang diharapkan',
            'type' => 'evaluasi_dosen',
            'academic_period_id' => $activePeriodId,
            'start_date' => '2024-09-10', // Sebelum UTS
            'end_date' => '2024-10-10',
            'is_active' => true,
        ]);

        // Kuesioner Evaluasi Layanan Tugas Akhir (ELTA)
        Questionnaire::create([
            'title' => 'Evaluasi Layanan Tugas Akhir (ELTA)',
            'slug' => Str::slug('Evaluasi Layanan Tugas Akhir (ELTA)'),
            'description' => 'Untuk mengetahui respon dan penilaian mahasiswa terhadap layanan pelaksanaan tugas akhir',
            'type' => 'elta',
            'academic_period_id' => $activePeriodId,
            'start_date' => '2024-09-10',
            'end_date' => '2024-12-20',
            'is_active' => true,
        ]);

        // Kuesioner Kepuasan Dosen
        Questionnaire::create([
            'title' => 'Kepuasan Dosen',
            'slug' => Str::slug('Kepuasan Dosen'),
            'description' => 'Mengetahui sejauh mana dosen puas dengan kinerja pengelola dan ketersediaan sarana prasarana di FIK UPNVJ',
            'type' => 'kepuasan_dosen',
            'academic_period_id' => $activePeriodId,
            'start_date' => '2024-09-10',
            'end_date' => '2024-12-20',
            'is_active' => true,
        ]);

        // Kuesioner Kepuasan Tenaga Kependidikan
        Questionnaire::create([
            'title' => 'Kepuasan Tenaga Kependidikan',
            'slug' => Str::slug('Kepuasan Tenaga Kependidikan'),
            'description' => 'Mengetahui respon dan penilaian tenaga kependidikan terhadap layanan yang diberikan oleh pengelola FIK UPNVJ',
            'type' => 'kepuasan_tendik',
            'academic_period_id' => $activePeriodId,
            'start_date' => '2024-09-10',
            'end_date' => '2024-12-20',
            'is_active' => true,
        ]);

        // Kuesioner Kepuasan Alumni
        Questionnaire::create([
            'title' => 'Kepuasan Alumni',
            'slug' => Str::slug('Kepuasan Alumni'),
            'description' => 'Mengetahui respon dan penilaian alumni terhadap layanan yang diberikan selama studi di FIK UPNVJ',
            'type' => 'kepuasan_alumni',
            'academic_period_id' => $activePeriodId,
            'start_date' => '2024-09-10',
            'end_date' => '2024-12-20',
            'is_active' => true,
        ]);

        // Kuesioner Kepuasan Pengguna Lulusan
        Questionnaire::create([
            'title' => 'Kepuasan Pengguna Lulusan',
            'slug' => Str::slug('Kepuasan Pengguna Lulusan'),
            'description' => 'Mengetahui sejauh mana pengguna lulusan merasa puas dengan kinerja, kompetensi, dan kontribusi lulusan FIK UPNVJ',
            'type' => 'kepuasan_pengguna_lulusan',
            'academic_period_id' => $activePeriodId,
            'start_date' => '2024-09-10',
            'end_date' => '2024-12-20',
            'is_active' => true,
        ]);

        // Kuesioner Kepuasan Mitra Kerjasama
        Questionnaire::create([
            'title' => 'Kepuasan Mitra Kerjasama',
            'slug' => Str::slug('Kepuasan Mitra Kerjasama'),
            'description' => 'Mengumpulkan umpan balik dari mitra eksternal mengenai kualitas dan efektivitas kerjasama',
            'type' => 'kepuasan_mitra',
            'academic_period_id' => $activePeriodId,
            'start_date' => '2024-09-10',
            'end_date' => '2024-12-20',
            'is_active' => true,
        ]);
    }
}
