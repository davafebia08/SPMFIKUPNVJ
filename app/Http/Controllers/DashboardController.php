<?php

namespace App\Http\Controllers;

use App\Models\Questionnaire;
use App\Models\Report;
use App\Models\Response; // Tambahkan import Response model
use App\Models\AcademicPeriod;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengalihkan berdasarkan peran pengguna
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if (Auth::user()->role === 'pimpinan') {
            return redirect()->route('pimpinan.dashboard');
        }

        $activePeriod = AcademicPeriod::where('is_active', true)->first();

        // Inisialisasi data sebagai array kosong
        $reports = [];
        $questionnaireSummary = [];

        if ($activePeriod) {
            // PERBAIKAN: Gunakan perhitungan langsung dari responses
            $questionnaires = Questionnaire::where('academic_period_id', $activePeriod->id)->get();

            foreach ($questionnaires as $questionnaire) {
                // Ambil responses untuk questionnaire ini
                $responses = Response::where('questionnaire_id', $questionnaire->id)->get();

                if (!$responses->isEmpty()) {
                    // Hitung data summary menggunakan metode yang konsisten
                    $averageTotal = $responses->average('rating');
                    $totalResponses = $responses->count();

                    // Hitung rata-rata per kategori
                    $categoryRatings = $responses->groupBy('question.category.name')
                        ->map(function ($items) {
                            return [
                                'average_rating' => $items->average('rating'),
                                'count' => $items->count()
                            ];
                        });

                    $categoryData = [];
                    foreach ($categoryRatings as $categoryName => $data) {
                        if ($categoryName) {
                            $categoryData[] = [
                                'category' => $categoryName,
                                'average_rating' => $data['average_rating'],
                                'count' => $data['count']
                            ];
                        }
                    }

                    // Hitung distribusi rating
                    $ratingDistribution = $responses->groupBy('rating')
                        ->map(function ($items) use ($responses) {
                            $count = $items->count();
                            $total = $responses->count();
                            return [
                                'count' => $count,
                                'percentage' => $total > 0 ? ($count / $total) * 100 : 0
                            ];
                        });

                    // Pastikan semua rating (1-4) terwakili
                    for ($i = 1; $i <= 4; $i++) {
                        if (!isset($ratingDistribution[$i])) {
                            $ratingDistribution[$i] = [
                                'count' => 0,
                                'percentage' => 0
                            ];
                        }
                    }

                    // Buat struktur data yang konsisten dengan format sebelumnya
                    $summaryData = [
                        'average_total' => $averageTotal,
                        'total_responses' => $totalResponses,
                        'category_data' => $categoryData,
                        'rating_distribution' => $ratingDistribution->sortKeys()->toArray()
                    ];

                    // Simpan dalam format yang kompatibel dengan view existing
                    $questionnaireSummary[$questionnaire->type] = [
                        'questionnaire' => $questionnaire,
                        'summary_data' => $summaryData,
                        'average_total' => $averageTotal,
                        'total_responses' => $totalResponses,
                        'category' => $this->getRatingCategory($averageTotal)
                    ];

                    // Untuk kompatibilitas dengan view yang menggunakan reports
                    $reports[$questionnaire->type] = collect([(object)[
                        'questionnaire' => $questionnaire,
                        'summary_data' => $summaryData,
                        'questionnaire_id' => $questionnaire->id,
                        'academic_period_id' => $questionnaire->academic_period_id
                    ]]);
                }
            }

            // Ambil kuesioner yang sudah diisi oleh pengguna saat ini
            $completedQuestionnaireIds = auth()->user()->questionnaires()
                ->wherePivotNotNull('submitted_at')
                ->pluck('questionnaire_id')
                ->toArray();

            // Ambil kuesioner yang tersedia untuk pengguna saat ini berdasarkan role
            // dan belum diisi oleh pengguna saat ini
            $userRole = auth()->user()->role;
            $availableQuestionnaires = Questionnaire::where('academic_period_id', $activePeriod->id)
                ->where('is_active', true)
                ->whereHas('permissions', function ($query) use ($userRole) {
                    $query->where('role', $userRole)
                        ->where('can_fill', true);
                })
                ->whereNotIn('id', $completedQuestionnaireIds)
                ->get();

            // Ambil kuesioner yang sudah diisi
            $completedQuestionnaires = auth()->user()->questionnaires()
                ->wherePivotNotNull('submitted_at')
                ->where('academic_period_id', $activePeriod->id)
                ->get();

            // Hitung jumlah responden untuk setiap kuesioner yang sudah diisi user
            foreach ($completedQuestionnaires as $questionnaire) {
                $respondentCount = DB::table('questionnaire_user')
                    ->where('questionnaire_id', $questionnaire->id)
                    ->whereNotNull('submitted_at')
                    ->count();

                $questionnaire->respondent_count = $respondentCount;
            }
        } else {
            $availableQuestionnaires = collect();
            $completedQuestionnaires = collect();
        }

        return view('dashboard.index', compact(
            'activePeriod',
            'reports',
            'questionnaireSummary',
            'availableQuestionnaires',
            'completedQuestionnaires'
        ));
    }

    /**
     * Helper method untuk mendapatkan kategori rating berdasarkan nilai
     */
    private function getRatingCategory($value)
    {
        if ($value >= 3.5) return 'Sangat Baik';
        if ($value >= 3.0) return 'Baik';
        if ($value >= 2.0) return 'Cukup';
        return 'Kurang';
    }

    /**
     * Method untuk mendapatkan statistik ringkas dashboard user
     */
    private function getDashboardStats($activePeriod)
    {
        if (!$activePeriod) {
            return [
                'total_questionnaires' => 0,
                'completed_questionnaires' => 0,
                'average_rating' => 0,
                'completion_rate' => 0
            ];
        }

        $userRole = auth()->user()->role;

        // Total kuesioner yang bisa diisi user
        $totalQuestionnaires = Questionnaire::where('academic_period_id', $activePeriod->id)
            ->where('is_active', true)
            ->whereHas('permissions', function ($query) use ($userRole) {
                $query->where('role', $userRole)
                    ->where('can_fill', true);
            })
            ->count();

        // Kuesioner yang sudah diisi
        $completedQuestionnaires = auth()->user()->questionnaires()
            ->wherePivotNotNull('submitted_at')
            ->where('academic_period_id', $activePeriod->id)
            ->count();

        // Rata-rata rating yang diberikan user
        $userResponses = Response::whereHas('questionnaire', function ($query) use ($activePeriod) {
            $query->where('academic_period_id', $activePeriod->id);
        })
            ->where('user_id', auth()->id())
            ->get();

        $averageRating = $userResponses->isNotEmpty() ? $userResponses->average('rating') : 0;

        // Completion rate
        $completionRate = $totalQuestionnaires > 0 ? ($completedQuestionnaires / $totalQuestionnaires) * 100 : 0;

        return [
            'total_questionnaires' => $totalQuestionnaires,
            'completed_questionnaires' => $completedQuestionnaires,
            'average_rating' => $averageRating,
            'completion_rate' => $completionRate
        ];
    }
}
