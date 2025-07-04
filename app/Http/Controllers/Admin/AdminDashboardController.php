<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\Questionnaire;
use App\Models\Report;
use App\Models\Response; // Tambahkan import ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Mendapatkan semua periode akademik untuk dropdown filter
        $academicPeriods = AcademicPeriod::orderBy('year', 'desc')->orderBy('semester', 'desc')->get();

        // Mendapatkan periode yang dipilih atau default ke periode aktif
        $selectedPeriodId = $request->input('academic_period_id', null);
        $selectedProdi = $request->input('program_studi', null);

        // Tentukan periode untuk filter
        $activePeriod = null;
        $periodFilter = null;
        $isAllPeriods = false;

        if ($selectedPeriodId === 'all') {
            $periodFilter = null;
            $isAllPeriods = true;
        } elseif ($selectedPeriodId) {
            $activePeriod = AcademicPeriod::findOrFail($selectedPeriodId);
            $periodFilter = $activePeriod->id;
        } else {
            $activePeriod = AcademicPeriod::where('is_active', true)->first();
            $periodFilter = $activePeriod ? $activePeriod->id : null;
            $selectedPeriodId = $periodFilter;
        }

        // Mempersiapkan data untuk dashboard
        $dashboardData = [
            'total_questionnaires' => 0,
            'total_respondents' => 0,
            'average_spm' => 0,
            'questionnaire_types' => [],
            'category_averages' => [],
            'period_trends' => []
        ];

        // Query untuk kuesioner
        $questionnaireQuery = Questionnaire::query();
        if ($periodFilter) {
            $questionnaireQuery->where('academic_period_id', $periodFilter);
        }
        $dashboardData['total_questionnaires'] = $questionnaireQuery->count();

        // Menghitung responden unik dengan filter program studi dan periode
        $respondentQuery = DB::table('questionnaire_user')
            ->join('questionnaires', 'questionnaire_user.questionnaire_id', '=', 'questionnaires.id')
            ->join('users', 'questionnaire_user.user_id', '=', 'users.id')
            ->whereNotNull('questionnaire_user.submitted_at');

        if ($periodFilter) {
            $respondentQuery->where('questionnaires.academic_period_id', $periodFilter);
        }

        if ($selectedProdi) {
            $respondentQuery->where('users.program_studi', $selectedProdi);
        }

        $dashboardData['total_respondents'] = $respondentQuery->distinct('questionnaire_user.user_id')
            ->count('questionnaire_user.user_id');

        // PERBAIKAN: Gunakan perhitungan langsung dari responses seperti di show method
        $questionnairesQuery = Questionnaire::query();
        if ($periodFilter) {
            $questionnairesQuery->where('academic_period_id', $periodFilter);
        }
        $questionnaires = $questionnairesQuery->get();

        $totalAverage = 0;
        $count = 0;

        foreach ($questionnaires as $questionnaire) {
            // Query responses untuk questionnaire ini
            $responsesQuery = Response::where('questionnaire_id', $questionnaire->id);

            // Filter program studi jika dipilih (kecuali untuk mitra dan pengguna lulusan)
            if ($selectedProdi && !in_array($questionnaire->type, ['kepuasan_pengguna_lulusan', 'kepuasan_mitra'])) {
                $responsesQuery->whereHas('user', function ($query) use ($selectedProdi) {
                    $query->where('program_studi', $selectedProdi);
                });
            }

            $responses = $responsesQuery->get();

            if ($responses->isEmpty()) {
                continue;
            }

            // Hitung rata-rata untuk questionnaire ini
            $questionnaireAverage = $responses->average('rating');

            if ($questionnaireAverage > 0) {
                $totalAverage += $questionnaireAverage;
                $count++;

                // Simpan data tipe kuesioner dengan perhitungan yang konsisten
                if (!isset($dashboardData['questionnaire_types'][$questionnaire->type])) {
                    $dashboardData['questionnaire_types'][$questionnaire->type] = [
                        'title' => $questionnaire->title,
                        'average' => $questionnaireAverage,
                        'category' => $this->getRatingCategory($questionnaireAverage),
                        'count' => 1,
                        'total' => $questionnaireAverage
                    ];
                } else {
                    // Agregasi untuk tipe yang sama
                    $existing = $dashboardData['questionnaire_types'][$questionnaire->type];
                    $newTotal = $existing['total'] + $questionnaireAverage;
                    $newCount = $existing['count'] + 1;
                    $newAverage = $newTotal / $newCount;

                    $dashboardData['questionnaire_types'][$questionnaire->type] = [
                        'title' => $questionnaire->title,
                        'average' => $newAverage,
                        'category' => $this->getRatingCategory($newAverage),
                        'count' => $newCount,
                        'total' => $newTotal
                    ];
                }

                // Hitung rata-rata kategori dengan cara yang sama
                $categoryRatings = $responses->groupBy('question.category.name')
                    ->map(function ($items) {
                        return $items->average('rating');
                    });

                foreach ($categoryRatings as $categoryName => $categoryAverage) {
                    if (!isset($dashboardData['category_averages'][$categoryName])) {
                        $dashboardData['category_averages'][$categoryName] = [
                            'total' => 0,
                            'count' => 0
                        ];
                    }

                    $dashboardData['category_averages'][$categoryName]['total'] += $categoryAverage;
                    $dashboardData['category_averages'][$categoryName]['count']++;
                }
            }
        }

        // Menghitung rata-rata SPM keseluruhan
        $dashboardData['average_spm'] = $count > 0 ? $totalAverage / $count : 0;

        // Menghitung rata-rata kategori akhir
        foreach ($dashboardData['category_averages'] as $category => $data) {
            $dashboardData['category_averages'][$category] = [
                'average' => $data['count'] > 0 ? $data['total'] / $data['count'] : 0,
                'category' => $this->getRatingCategory($data['count'] > 0 ? $data['total'] / $data['count'] : 0)
            ];
        }

        // Mendapatkan data tren periode (tetap menggunakan report karena untuk historical data)
        $lastPeriods = AcademicPeriod::orderBy('year', 'desc')
            ->orderBy('semester', 'desc')
            ->limit(5)
            ->get();

        foreach ($lastPeriods as $period) {
            $periodReports = Report::where('academic_period_id', $period->id)->get();
            $periodTotal = 0;
            $periodCount = 0;

            foreach ($periodReports as $report) {
                $summaryData = is_string($report->summary_data) ?
                    json_decode($report->summary_data, true) : $report->summary_data;

                if (isset($summaryData['average_total'])) {
                    $periodTotal += floatval($summaryData['average_total']);
                    $periodCount++;
                }
            }

            $periodAverage = $periodCount > 0 ? $periodTotal / $periodCount : 0;

            $dashboardData['period_trends'][] = [
                'period' => $period->name,
                'average' => $periodAverage,
                'category' => $this->getRatingCategory($periodAverage)
            ];
        }

        $dashboardData['period_trends'] = array_reverse($dashboardData['period_trends']);

        $programStudiOptions = [
            'S1 Informatika' => 'S1 Informatika',
            'S1 Sistem Informasi' => 'S1 Sistem Informasi',
            'S1 Sains Data' => 'S1 Sains Data',
            'D3 Sistem Informasi' => 'D3 Sistem Informasi'
        ];

        return view('admin.dashboard.index', compact('academicPeriods', 'activePeriod', 'selectedPeriodId', 'selectedProdi', 'programStudiOptions', 'dashboardData', 'isAllPeriods'));
    }

    private function getRatingCategory($value)
    {
        if ($value >= 3.5) return 'Sangat Baik';
        if ($value >= 3.0) return 'Baik';
        if ($value >= 2.0) return 'Cukup';
        return 'Kurang';
    }
}
