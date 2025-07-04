<?php

namespace Database\Seeders;

use App\Models\AcademicPeriod;
use Illuminate\Database\Seeder;

class AcademicPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Periode Sebelumnya
        AcademicPeriod::create([
            'name' => 'Semester Ganjil 2023/2024',
            'semester' => 'Ganjil',
            'year' => '2023/2024',
            'start_date' => '2023-09-01',
            'end_date' => '2024-01-31',
            'is_active' => false,
        ]);

        AcademicPeriod::create([
            'name' => 'Semester Genap 2023/2024',
            'semester' => 'Genap',
            'year' => '2023/2024',
            'start_date' => '2024-02-01',
            'end_date' => '2024-06-30',
            'is_active' => false,
        ]);

        // Periode Aktif
        AcademicPeriod::create([
            'name' => 'Semester Ganjil 2024/2025',
            'semester' => 'Ganjil',
            'year' => '2024/2025',
            'start_date' => '2024-09-01',
            'end_date' => '2025-01-31',
            'is_active' => true,
        ]);
    }
}
