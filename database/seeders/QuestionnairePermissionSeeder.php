<?php

namespace Database\Seeders;

use App\Models\QuestionnairePermission;
use Illuminate\Database\Seeder;

class QuestionnairePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mapping kuesioner dengan role yang diizinkan untuk mengisi
        $permissions = [
            1 => ['mahasiswa', 'dosen', 'tendik'], // Evaluasi Layanan Fakultas (Mahasiswa, Dosen, Tendik)
            2 => ['mahasiswa'], // ELOM (Mahasiswa)
            3 => ['mahasiswa'], // Evaluasi Dosen (Mahasiswa)
            4 => ['mahasiswa'], // ELTA (Mahasiswa)
            5 => ['dosen'], // Kepuasan Dosen (Dosen)
            6 => ['tendik'], // Kepuasan Tendik (Tendik)
            7 => ['alumni'], // Kepuasan Alumni (Alumni)
            8 => ['pengguna_lulusan'], // Kepuasan Pengguna Lulusan (Pengguna Lulusan)
            9 => ['mitra'], // Kepuasan Mitra (Mitra)
        ];

        // Set permission untuk semua kuesioner
        foreach ($permissions as $questionnaire_id => $roles) {
            // Permission untuk role user
            foreach ($roles as $role) {
                QuestionnairePermission::create([
                    'questionnaire_id' => $questionnaire_id,
                    'role' => $role,
                    'can_fill' => true,
                    'can_view_results' => $role == 'dosen', // Dosen bisa melihat hasil semua kuesioner
                ]);
            }

            // Permission untuk admin dan pimpinan (hanya lihat hasil)
            QuestionnairePermission::create([
                'questionnaire_id' => $questionnaire_id,
                'role' => 'admin',
                'can_fill' => false,
                'can_view_results' => true,
            ]);

            QuestionnairePermission::create([
                'questionnaire_id' => $questionnaire_id,
                'role' => 'pimpinan',
                'can_fill' => false,
                'can_view_results' => true,
            ]);
        }
    }
}
