<?php

namespace Database\Seeders;

use App\Models\AcademicPeriod;
use App\Models\Questionnaire;
use App\Models\Report;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin ID untuk generated_by
        $adminId = 1;

        // Ambil periode aktif
        $academicPeriod = AcademicPeriod::where('is_active', true)->first();

        // Ambil semua kuesioner untuk periode aktif
        $questionnaires = Questionnaire::where('academic_period_id', $academicPeriod->id)->get();

        foreach ($questionnaires as $questionnaire) {
            // Hitung rata-rata per kategori
            $categoryData = DB::table('responses')
                ->join('questions', 'responses.question_id', '=', 'questions.id')
                ->join('questionnaire_categories', 'questions.category_id', '=', 'questionnaire_categories.id')
                ->where('responses.questionnaire_id', $questionnaire->id)
                ->select(
                    'questionnaire_categories.name as category',
                    DB::raw('AVG(responses.rating) as average_rating'),
                    DB::raw('COUNT(DISTINCT responses.user_id) as total_respondents')
                )
                ->groupBy('questionnaire_categories.name')
                ->get();

            // Hitung rata-rata per pertanyaan
            $questionData = DB::table('responses')
                ->join('questions', 'responses.question_id', '=', 'questions.id')
                ->join('questionnaire_categories', 'questions.category_id', '=', 'questionnaire_categories.id')
                ->where('responses.questionnaire_id', $questionnaire->id)
                ->select(
                    'questions.id',
                    'questions.question',
                    'questionnaire_categories.name as category',
                    DB::raw('AVG(responses.rating) as average_rating'),
                    DB::raw('COUNT(responses.id) as total_responses')
                )
                ->groupBy('questions.id', 'questions.question', 'questionnaire_categories.name')
                ->get();

            // Hitung rata-rata total
            $averageTotal = DB::table('responses')
                ->where('questionnaire_id', $questionnaire->id)
                ->avg('rating');

            $totalRespondents = DB::table('questionnaire_user')
                ->where('questionnaire_id', $questionnaire->id)
                ->whereNotNull('submitted_at')
                ->count();

            // Distribusi rating
            $ratingDistribution = DB::table('responses')
                ->where('questionnaire_id', $questionnaire->id)
                ->select('rating', DB::raw('COUNT(*) as count'))
                ->groupBy('rating')
                ->orderBy('rating', 'desc')
                ->pluck('count', 'rating')
                ->toArray();

            // Jumlah responden per kategori
            $respondentsByRole = DB::table('questionnaire_user')
                ->join('users', 'questionnaire_user.user_id', '=', 'users.id')
                ->where('questionnaire_user.questionnaire_id', $questionnaire->id)
                ->whereNotNull('questionnaire_user.submitted_at')
                ->select('users.role', DB::raw('COUNT(*) as count'))
                ->groupBy('users.role')
                ->pluck('count', 'role')
                ->toArray();

            // Data ringkasan
            $summaryData = [
                'category_data' => $categoryData,
                'question_data' => $questionData,
                'average_total' => $averageTotal,
                'total_respondents' => $totalRespondents,
                'rating_distribution' => $ratingDistribution,
                'respondents_by_role' => $respondentsByRole
            ];

            // Buat laporan
            Report::create([
                'academic_period_id' => $academicPeriod->id,
                'questionnaire_id' => $questionnaire->id,
                'summary_data' => $summaryData,
                'notes' => "Laporan {$questionnaire->title} periode {$academicPeriod->name}.",
                'generated_by' => $adminId,
                'generated_at' => now()->subDays(rand(1, 10))
            ]);
        }
    }
}
