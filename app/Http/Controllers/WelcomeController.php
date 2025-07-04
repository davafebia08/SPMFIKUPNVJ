<?php

namespace App\Http\Controllers;

use App\Models\AcademicPeriod;
use App\Models\Questionnaire;
use App\Models\Report;
use App\Models\Response; // Tambahkan import Response model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WelcomeController extends Controller
{
    public function index()
    {
        // Ambil periode akademik aktif
        $activePeriod = AcademicPeriod::where('is_active', true)->first();

        if (!$activePeriod) {
            // Jika tidak ada periode aktif, ambil periode terbaru
            $activePeriod = AcademicPeriod::orderBy('year', 'desc')
                ->orderBy('semester', 'desc')
                ->first();
        }

        // Jika tidak ada data periode sama sekali
        if (!$activePeriod) {
            return view('welcome', ['dashboardData' => []]);
        }

        $dashboardData = [
            'total_questionnaires' => 0,
            'total_respondents' => 0,
            'average_spm' => 0,
            'questionnaire_types' => [],
            'category_averages' => [],
            'period_trends' => [],
            'active_period' => $activePeriod->name
        ];

        // Hitung total kuesioner
        $dashboardData['total_questionnaires'] = Questionnaire::where('academic_period_id', $activePeriod->id)->count();

        // Hitung total responden
        $dashboardData['total_respondents'] = DB::table('questionnaire_user')
            ->join('questionnaires', 'questionnaire_user.questionnaire_id', '=', 'questionnaires.id')
            ->where('questionnaires.academic_period_id', $activePeriod->id)
            ->whereNotNull('questionnaire_user.submitted_at')
            ->distinct('questionnaire_user.user_id')
            ->count('questionnaire_user.user_id');

        // Define questionnaire types
        $questionnaireTypes = [
            'layanan_fakultas',
            'elom',
            'evaluasi_dosen',
            'elta',
            'kepuasan_dosen',
            'kepuasan_tendik',
            'kepuasan_alumni',
            'kepuasan_pengguna_lulusan',
            'kepuasan_mitra',
        ];

        // Siapkan array kosong untuk tipe kuesioner
        foreach ($questionnaireTypes as $type) {
            $dashboardData['questionnaire_types'][$type] = [
                'title' => $this->getQuestionnaireTitle($type),
                'average' => 0,
                'category' => 'Belum Tersedia'
            ];
        }

        // PERBAIKAN: Gunakan perhitungan langsung dari responses
        $questionnaires = Questionnaire::where('academic_period_id', $activePeriod->id)->get();

        $totalAverage = 0;
        $count = 0;

        foreach ($questionnaires as $questionnaire) {
            // Query responses untuk questionnaire ini
            $responses = Response::where('questionnaire_id', $questionnaire->id)->get();

            if ($responses->isEmpty()) {
                continue;
            }

            // Hitung rata-rata untuk questionnaire ini
            $questionnaireAverage = $responses->average('rating');

            if ($questionnaireAverage > 0) {
                $totalAverage += $questionnaireAverage;
                $count++;

                // Update data tipe kuesioner dengan perhitungan yang konsisten
                $dashboardData['questionnaire_types'][$questionnaire->type] = [
                    'title' => $questionnaire->title,
                    'average' => $questionnaireAverage,
                    'category' => $this->getRatingCategory($questionnaireAverage)
                ];

                // Hitung rata-rata kategori dengan cara yang sama seperti show method
                $categoryRatings = $responses->groupBy('question.category.name')
                    ->map(function ($items) {
                        return $items->average('rating');
                    });

                foreach ($categoryRatings as $categoryName => $categoryAverage) {
                    if (!$categoryName || $categoryAverage <= 0) continue;

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

        $dashboardData['average_spm'] = $count > 0 ? $totalAverage / $count : 0;

        // Hitung rata-rata per kategori
        foreach ($dashboardData['category_averages'] as $category => $data) {
            $dashboardData['category_averages'][$category] = [
                'average' => $data['count'] > 0 ? $data['total'] / $data['count'] : 0,
                'category' => $this->getRatingCategory($data['count'] > 0 ? $data['total'] / $data['count'] : 0)
            ];
        }

        // Tren periode terakhir (tetap menggunakan reports untuk historical data)
        $lastPeriods = AcademicPeriod::orderBy('year', 'desc')
            ->orderBy('semester', 'desc')
            ->limit(5)
            ->get();

        foreach ($lastPeriods as $period) {
            // Gunakan perhitungan yang sama seperti periode aktif
            $periodQuestionnaires = Questionnaire::where('academic_period_id', $period->id)->get();
            $periodTotal = 0;
            $periodCount = 0;

            foreach ($periodQuestionnaires as $questionnaire) {
                $responses = Response::where('questionnaire_id', $questionnaire->id)->get();

                if (!$responses->isEmpty()) {
                    $questionnaireAverage = $responses->average('rating');
                    if ($questionnaireAverage > 0) {
                        $periodTotal += $questionnaireAverage;
                        $periodCount++;
                    }
                }
            }

            $periodAverage = $periodCount > 0 ? $periodTotal / $periodCount : 0;

            $dashboardData['period_trends'][] = [
                'period' => $period->name,
                'average' => $periodAverage,
                'category' => $this->getRatingCategory($periodAverage)
            ];
        }

        // Urutkan tren periode
        $dashboardData['period_trends'] = array_reverse($dashboardData['period_trends']);

        return view('welcome', compact('dashboardData'));
    }

    private function getRatingCategory($value)
    {
        if ($value >= 3.5) return 'Sangat Baik';
        if ($value >= 3.0) return 'Baik';
        if ($value >= 2.0) return 'Cukup';
        return 'Kurang';
    }

    private function getQuestionnaireTitle($type)
    {
        $titles = [
            'layanan_fakultas' => 'Evaluasi Layanan Fakultas',
            'elom' => 'Evaluasi Layanan oleh Mahasiswa (ELOM)',
            'evaluasi_dosen' => 'Evaluasi Dosen oleh Mahasiswa',
            'elta' => 'Evaluasi Layanan Tugas Akhir (ELTA)',
            'kepuasan_dosen' => 'Kepuasan Dosen',
            'kepuasan_tendik' => 'Kepuasan Tenaga Kependidikan',
            'kepuasan_alumni' => 'Kepuasan Alumni',
            'kepuasan_pengguna_lulusan' => 'Kepuasan Pengguna Lulusan',
            'kepuasan_mitra' => 'Kepuasan Mitra Kerjasama'
        ];

        return $titles[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }
}
