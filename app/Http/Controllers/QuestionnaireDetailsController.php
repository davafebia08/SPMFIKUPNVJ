<?php

namespace App\Http\Controllers;

use App\Models\Questionnaire;
use App\Models\Report;
use App\Models\Response; // Tambahkan import Response model
use App\Models\AcademicPeriod;
use App\Models\QuestionnaireCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionnaireDetailsController extends Controller
{
    public function show($type)
    {
        // Ambil periode akademik aktif
        $activePeriod = AcademicPeriod::where('is_active', true)->first();

        if (!$activePeriod) {
            return redirect()->route('dashboard')->with('error', 'Tidak ada periode akademik aktif');
        }

        // Ambil kuesioner berdasarkan tipe
        $questionnaire = Questionnaire::where('type', $type)
            ->where('academic_period_id', $activePeriod->id)
            ->where('is_active', true)
            ->first();

        if (!$questionnaire) {
            return redirect()->route('dashboard')->with('error', 'Kuesioner tidak ditemukan');
        }

        // Ambil kategori kuesioner
        $categories = QuestionnaireCategory::all();

        // PERBAIKAN: Gunakan perhitungan langsung dari responses
        $responses = Response::where('questionnaire_id', $questionnaire->id)->get();

        $categoryAverages = [];
        $chartData = null;

        if (!$responses->isEmpty()) {
            // Hitung rata-rata per kategori menggunakan metode yang sama seperti show method
            $categoryRatings = $responses->groupBy('question.category.name')
                ->map(function ($items) {
                    return [
                        'average_rating' => $items->average('rating'),
                        'count' => $items->count()
                    ];
                });

            foreach ($categoryRatings as $categoryName => $data) {
                if ($categoryName) {
                    $categoryAverages[$categoryName] = $data['average_rating'];
                }
            }

            // Buat chart data yang konsisten dengan format yang diharapkan
            $chartData = [
                'average_total' => $responses->average('rating'),
                'total_responses' => $responses->count(),
                'category_data' => []
            ];

            foreach ($categoryRatings as $categoryName => $data) {
                if ($categoryName) {
                    $chartData['category_data'][] = [
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

            $chartData['rating_distribution'] = $ratingDistribution->sortKeys()->toArray();
        } else {
            // Jika tidak ada responses, gunakan data kosong atau default
            foreach ($categories as $category) {
                $categoryAverages[$category->name] = 0;
            }

            $chartData = [
                'average_total' => 0,
                'total_responses' => 0,
                'category_data' => [],
                'rating_distribution' => [
                    1 => ['count' => 0, 'percentage' => 0],
                    2 => ['count' => 0, 'percentage' => 0],
                    3 => ['count' => 0, 'percentage' => 0],
                    4 => ['count' => 0, 'percentage' => 0]
                ]
            ];
        }

        // Hitung jumlah responden
        $respondentCount = DB::table('questionnaire_user')
            ->where('questionnaire_id', $questionnaire->id)
            ->whereNotNull('submitted_at')
            ->count();

        // Title dan description berdasarkan tipe kuesioner
        $titles = [
            'layanan_fakultas' => 'Kepuasan Layanan Fakultas',
            'elom' => 'Evaluasi Layanan oleh Mahasiswa (ELOM)',
            'evaluasi_dosen' => 'Evaluasi Dosen oleh Mahasiswa',
            'elta' => 'Evaluasi Layanan Tugas Akhir (ELTA)',
            'kepuasan_dosen' => 'Kepuasan Dosen',
            'kepuasan_tendik' => 'Kepuasan Tenaga Kependidikan',
            'kepuasan_alumni' => 'Kepuasan Alumni',
            'kepuasan_pengguna_lulusan' => 'Kepuasan Pengguna Lulusan',
            'kepuasan_mitra' => 'Kepuasan Mitra Kerjasama'
        ];

        $descriptions = [
            'layanan_fakultas' => 'Mengukur tingkat kepuasan terhadap layanan fakultas secara umum',
            'elom' => 'Mengukur tingkat kepuasan mahasiswa terhadap layanan fakultas',
            'evaluasi_dosen' => 'Mengukur tingkat kepuasan mahasiswa terhadap kinerja dosen',
            'elta' => 'Mengukur tingkat kepuasan mahasiswa terhadap layanan tugas akhir',
            'kepuasan_dosen' => 'Mengukur tingkat kepuasan dosen terhadap layanan fakultas',
            'kepuasan_tendik' => 'Mengukur tingkat kepuasan tenaga kependidikan terhadap layanan fakultas',
            'kepuasan_alumni' => 'Mengukur tingkat kepuasan alumni terhadap layanan fakultas',
            'kepuasan_pengguna_lulusan' => 'Mengukur tingkat kepuasan pengguna lulusan terhadap kualitas lulusan',
            'kepuasan_mitra' => 'Mengukur tingkat kepuasan mitra kerjasama terhadap layanan fakultas'
        ];

        $tujuan = "Mengukur dan memahami tingkat kepuasan pengguna terhadap layanan di FIK UPNVJ agar pihak fakultas dapat mengevaluasi kekuatan dan kelemahan layanan serta merumuskan strategi peningkatan berdasarkan data yang akurat.";

        return view('questionnaire.details', compact(
            'questionnaire',
            'type',
            'categories',
            'categoryAverages',
            'chartData',
            'titles',
            'descriptions',
            'tujuan',
            'respondentCount'
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
}
