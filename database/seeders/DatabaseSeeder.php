<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,            // User admin, pimpinan, tendik, mahasiswa, dll
            DosenApiSeeder::class,        // Data dosen dari API UPNVJ
            AcademicPeriodSeeder::class,
            QuestionnaireCategorySeeder::class,
            QuestionnaireSeeder::class,
            QuestionSeeder::class,
            QuestionnairePermissionSeeder::class,
            ResponseSeeder::class,
            ReportSeeder::class,
        ]);
    }
}
