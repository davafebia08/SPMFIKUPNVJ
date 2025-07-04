<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Questionnaire;
use App\Models\Response;
use App\Models\Question;
use App\Models\QuestionnaireCategory;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\QuestionnaireResultsExport;


class PimpinanReportController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua periode akademik untuk dropdown filter
        $academicPeriods = \App\Models\AcademicPeriod::orderBy('year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        // Mendapatkan periode yang dipilih atau default ke semua
        $selectedPeriodId = $request->input('academic_period_id', null);
        $selectedPeriod = null;

        // Query kuesioner berdasarkan filter periode
        $query = \App\Models\Questionnaire::with('academicPeriod');

        if ($selectedPeriodId) {
            $selectedPeriod = \App\Models\AcademicPeriod::find($selectedPeriodId);
            $query->where('academic_period_id', $selectedPeriodId);
        }

        $questionnaires = $query->orderBy('created_at', 'desc')->get();

        // Tambahkan jumlah responden ke setiap kuesioner
        foreach ($questionnaires as $questionnaire) {
            // Hitung jumlah responden unik
            $responderCount = DB::table('questionnaire_user')
                ->where('questionnaire_id', $questionnaire->id)
                ->whereNotNull('submitted_at')
                ->count();

            $questionnaire->respondent_count = $responderCount;
        }

        return view('pimpinan.reports.index', compact('questionnaires', 'academicPeriods', 'selectedPeriod'));
    }

    public function show($id)
    {
        $questionnaire = Questionnaire::findOrFail($id);
        $responses = Response::where('questionnaire_id', $id)->get();

        // Ambil data report dari database jika ada
        $report = Report::where('questionnaire_id', $id)->first();

        // Hitung jumlah responden unik
        $uniqueResponderCount = DB::table('questionnaire_user')
            ->where('questionnaire_id', $id)
            ->whereNotNull('submitted_at')
            ->count();

        // Grup pertanyaan berdasarkan kategori
        $categories = QuestionnaireCategory::all();
        $questions = Question::where('questionnaire_id', $id)
            ->orderBy('category_id')
            ->orderBy('order')
            ->get();

        // Hitung statistik untuk setiap pertanyaan
        $questionStats = [];

        foreach ($questions as $question) {
            // Ambil jawaban dari tabel responses, bukan answers
            $answers = DB::table('responses')
                ->select('rating as value', DB::raw('count(*) as total'))
                ->where('question_id', $question->id)
                ->where('questionnaire_id', $id)
                ->groupBy('rating')
                ->get();

            $stats = [
                1 => 0, // Kurang
                2 => 0, // Cukup
                3 => 0, // Baik
                4 => 0, // Sangat Baik
            ];

            $total = 0;
            $sum = 0;

            foreach ($answers as $answer) {
                $value = (int)$answer->value;
                if ($value >= 1 && $value <= 4) {
                    $stats[$value] = $answer->total;
                    $total += $answer->total;
                    $sum += $value * $answer->total;
                }
            }

            $average = $total > 0 ? $sum / $total : 0;

            $questionStats[$question->id] = [
                'stats' => $stats,
                'average' => $average,
                'total' => $total
            ];
        }

        // Hitung rata-rata untuk setiap kategori
        $categoryStats = [];
        foreach ($categories as $category) {
            $categoryQuestions = $questions->where('category_id', $category->id);
            $totalAverage = 0;
            $questionCount = 0;

            foreach ($categoryQuestions as $question) {
                if (isset($questionStats[$question->id])) {
                    $totalAverage += $questionStats[$question->id]['average'];
                    $questionCount++;
                }
            }

            $categoryAverage = $questionCount > 0 ? $totalAverage / $questionCount : 0;

            $categoryStats[$category->id] = [
                'name' => $category->name,
                'average' => $categoryAverage,
                'questions' => $categoryQuestions
            ];
        }

        // Menghitung rata-rata keseluruhan
        $overallAverage = $responses->average('rating') ?? 0;

        return view('pimpinan.reports.show', compact(
            'questionnaire',
            'responses',
            'categories',
            'questions',
            'questionStats',
            'categoryStats',
            'overallAverage',
            'report'
        ));
    }

    public function exportExcel($id)
    {
        $questionnaire = Questionnaire::findOrFail($id);

        // Generate filename
        $filename = 'Laporan_' . Str::slug($questionnaire->title) . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        try {
            return Excel::download(
                new QuestionnaireResultsExport($id),
                $filename,
                \Maatwebsite\Excel\Excel::XLSX
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }

    public function updateContent(Request $request, $id)
    {
        $questionnaire = Questionnaire::findOrFail($id);

        // Validasi input
        $validated = $request->validate([
            'analysis_content' => 'nullable|string',
            'conclusion_content' => 'nullable|string',
            'followup_content' => 'nullable|string',
        ]);

        // Cari report yang ada atau buat baru
        $report = Report::where('questionnaire_id', $id)->first();

        if (!$report) {
            $report = new Report();
            $report->questionnaire_id = $id;
            $report->academic_period_id = $questionnaire->academic_period_id;
            $report->summary_data = json_encode([]);
            $report->generated_by = Auth::id();
        }

        // Update konten
        $report->analysis_content = $validated['analysis_content'];
        $report->conclusion_content = $validated['conclusion_content'];
        $report->followup_content = $validated['followup_content'];
        $report->save();

        return redirect()
            ->route('pimpinan.reports.show', $id)
            ->with('success', 'Konten laporan berhasil diperbarui');
    }

    private function getLampiranImages()
    {
        // Contoh gambar placeholder
        return [
            asset('assets/img/data-placeholder1.png'),
            asset('assets/img/data-placeholder2.png'),
        ];
    }

    private function getSurveyType($type)
    {
        $types = [
            'edom' => 'EDOM (Evaluasi Dosen oleh Mahasiswa)',
            'layanan_fakultas' => 'Evaluasi Layanan Fakultas',
            'elom' => 'ELOM (Evaluasi Layanan oleh Mahasiswa)',
            'elta' => 'ELTA (Evaluasi Layanan Tugas Akhir)',
            'kepuasan_dosen' => 'Kepuasan Dosen',
            'kepuasan_tendik' => 'Kepuasan Tenaga Kependidikan',
            'kepuasan_alumni' => 'Kepuasan Alumni',
            'kepuasan_pengguna' => 'Kepuasan Pengguna Lulusan',
            'kepuasan_mitra' => 'Kepuasan Mitra Kerjasama',
        ];

        return $types[$type] ?? $type;
    }

    private function getRespondenType($type)
    {
        $types = [
            'edom' => 'mahasiswa',
            'layanan_fakultas' => 'stakeholder',
            'elom' => 'mahasiswa',
            'elta' => 'mahasiswa',
            'kepuasan_dosen' => 'dosen',
            'kepuasan_tendik' => 'tenaga kependidikan',
            'kepuasan_alumni' => 'alumni',
            'kepuasan_pengguna' => 'pengguna lulusan',
            'kepuasan_mitra' => 'mitra kerjasama',
        ];

        return $types[$type] ?? 'responden';
    }

    private function getSpecificContent($type)
    {
        $content = [];

        switch ($type) {
            case 'edom':
                $content = [
                    'latar_belakang' => 'Kuesioner kepuasan evaluasi dosen oleh mahasiswa disusun untuk mengetahui bagaimana respon dan penilaian mahasiswa terhadap aktifitas pembelajaran selama 1 semester untuk suatu mata kuliah tertentu. EDOM dilakukan 2 (dua) kali dalam 1 semester, yaitu sebelum Ujian Tengah Semester dan sebelum Ujian Akhir Semester. Pelaksanaan EDOM melalui Sistem Informasi Akademik (SIAKAD).',
                    'object' => 'kinerja dosen dalam proses pembelajaran',
                    'improvement_for' => 'pengembangan profesionalisme dosen, peningkatan metode pengajaran, serta perbaikan kurikulum yang lebih responsif terhadap kebutuhan mahasiswa',
                    'stakeholders' => 'para dosen yang dengan terbuka menerima hasil evaluasi ini sebagai bagian dari upaya untuk terus meningkatkan kualitas pengajaran',
                ];
                break;

            case 'layanan_fakultas':
                $content = [
                    'latar_belakang' => 'Kuesioner Layanan Fakultas disusun untuk mengukur dan memahami tingkat kepuasan pengguna terhadap layanan di FIK UPNVJ agar pihak fakultas dapat mengevaluasi kekuatan dan kelemahan layanan serta merumuskan strategi peningkatan berdasarkan data yang akurat.',
                    'object' => 'layanan fakultas',
                    'improvement_for' => 'peningkatan kualitas layanan fakultas',
                    'stakeholders' => 'pihak fakultas dan staf yang telah bekerja keras untuk memberikan layanan terbaik',
                ];
                break;

            case 'elom':
                $content = [
                    'latar_belakang' => 'Kuesioner Evaluasi Layanan oleh Mahasiswa disusun untuk menilai kinerja layanan yang diberikan FIK UPNVJ kepada Mahasiswa.',
                    'object' => 'layanan fakultas dari perspektif mahasiswa',
                    'improvement_for' => 'peningkatan kualitas layanan fakultas dan pengalaman mahasiswa',
                    'stakeholders' => 'pihak fakultas dan staf yang telah bekerja keras untuk memberikan layanan terbaik kepada mahasiswa',
                ];
                break;

            case 'elta':
                $content = [
                    'latar_belakang' => 'Kuesioner Evaluasi Layanan Tugas Akhir (ELTA) disusun untuk mengetahui bagaimana respon dan penilaian mahasiswa terhadap layanan pelaksanaan tugas akhir (PKL, Skripsi, atau Thesis). Fokus survey ini adalah penilaian terhadap program studi yang mengelola pelaksanaan tugas akhir.',
                    'object' => 'layanan pelaksanaan tugas akhir',
                    'improvement_for' => 'peningkatan kualitas layanan tugas akhir bagi mahasiswa',
                    'stakeholders' => 'program studi dan dosen pembimbing yang telah bekerja keras dalam membimbing mahasiswa',
                ];
                break;

            case 'kepuasan_dosen':
                $content = [
                    'latar_belakang' => 'Kuesioner Kepuasan Dosen disusun untuk mengetahui sejauh mana dosen puas dengan kinerja pengelola dan ketersediaan sarana prasarana di FIK UPNVJ.',
                    'object' => 'kinerja pengelola dan ketersediaan sarana prasarana',
                    'improvement_for' => 'peningkatan kualitas layanan dan fasilitas untuk dosen',
                    'stakeholders' => 'pihak fakultas yang telah bekerja keras untuk memenuhi kebutuhan dosen',
                ];
                break;

            case 'kepuasan_tendik':
                $content = [
                    'latar_belakang' => 'Kuesioner Kepuasan Tenaga Kependidikan disusun untuk mengetahui bagaimana respon dan penilaian tenaga kependidikan terhadap layanan yang diberikan oleh pengelola FIK UPNVJ.',
                    'object' => 'layanan yang diberikan oleh pengelola fakultas',
                    'improvement_for' => 'peningkatan kualitas layanan dan fasilitas untuk tenaga kependidikan',
                    'stakeholders' => 'pihak fakultas yang telah bekerja keras untuk memenuhi kebutuhan tenaga kependidikan',
                ];
                break;

            case 'kepuasan_alumni':
                $content = [
                    'latar_belakang' => 'Kuesioner Kepuasan Alumni disusun untuk mengetahui bagaimana respon dan penilaian alumni/lulusan terhadap layanan yang diberikan oleh FIK UPNVJ untuk kegiatan akademik dan non akademik selama melakukan studi di UPNVJ.',
                    'object' => 'layanan akademik dan non akademik',
                    'improvement_for' => 'peningkatan kualitas layanan akademik dan non akademik',
                    'stakeholders' => 'pihak fakultas dan program studi yang telah bekerja keras dalam memberikan layanan',
                ];
                break;

            case 'kepuasan_pengguna':
                $content = [
                    'latar_belakang' => 'Kuesioner Kepuasan Pengguna Lulusan disusun untuk mengetahui sejauh mana pengguna lulusan merasa puas dengan kinerja, kompetensi, dan kontribusi lulusan Fakultas Ilmu Komputer di tempat kerja.',
                    'object' => 'kinerja, kompetensi, dan kontribusi lulusan',
                    'improvement_for' => 'peningkatan kurikulum dan program pendidikan',
                    'stakeholders' => 'pihak fakultas dan program studi yang telah bekerja keras dalam mendidik lulusan',
                ];
                break;

            case 'kepuasan_mitra':
                $content = [
                    'latar_belakang' => 'Kuesioner Kepuasan Mitra Kerjasama disusun untuk mengumpulkan umpan balik yang mendalam dan konstruktif dari mitra eksternal mengenai kualitas dan efektivitas kerjasama yang telah terjalin.',
                    'object' => 'kualitas dan efektivitas kerjasama',
                    'improvement_for' => 'peningkatan kualitas kerjasama dengan mitra',
                    'stakeholders' => 'pihak fakultas yang telah bekerja keras dalam menjalin kerjasama',
                ];
                break;

            default:
                $content = [
                    'latar_belakang' => 'Kuesioner ini disusun untuk mengetahui tingkat kepuasan stakeholder terhadap layanan yang diberikan oleh FIK UPNVJ.',
                    'object' => 'layanan yang diberikan',
                    'improvement_for' => 'peningkatan kualitas layanan',
                    'stakeholders' => 'semua pihak yang telah bekerja keras dalam memberikan pelayanan',
                ];
        }

        return $content;
    }

    private function generateChartImage($title, $stats, $total)
    {
        // Buat gambar dasar dengan ukuran yang lebih baik untuk penampilan dalam PDF
        $width = 900;
        $height = 500;
        $image = imagecreatetruecolor($width, $height);

        // Isi warna latar (putih)
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgColor);

        // Atur warna untuk grafik dengan nuansa yang lebih profesional
        $colors = [
            1 => imagecolorallocate($image, 231, 76, 60),   // Merah - Kurang
            2 => imagecolorallocate($image, 241, 196, 15),  // Kuning - Cukup
            3 => imagecolorallocate($image, 46, 204, 113),  // Hijau - Baik
            4 => imagecolorallocate($image, 52, 152, 219),  // Biru - Sangat Baik
        ];

        // Atur warna untuk elemen lain
        $textColor = imagecolorallocate($image, 44, 62, 80);     // Warna teks utama
        $gridColor = imagecolorallocate($image, 220, 220, 220);  // Warna garis grid
        $borderColor = imagecolorallocate($image, 189, 195, 199); // Warna border

        // Atur margin
        $marginLeft = 80;
        $marginRight = 60;
        $marginTop = 80;
        $marginBottom = 100;

        // Area grafik
        $chartWidth = $width - $marginLeft - $marginRight;
        $chartHeight = $height - $marginTop - $marginBottom;

        // Gambar judul dengan font lebih besar
        $titleFontSize = 5; // Font size lebih besar
        $titleText = (strlen($title) > 70) ? substr($title, 0, 67) . "..." : $title;

        // Posisikan judul di tengah
        $titleWidth = strlen($titleText) * imagefontwidth($titleFontSize);
        $titleX = ($width - $titleWidth) / 2;
        imagestring($image, $titleFontSize, $titleX, 20, $titleText, $textColor);

        // Hitung nilai maksimum dan tambahkan sedikit padding
        $maxValue = max($stats);
        if ($maxValue == 0) $maxValue = 1;
        $maxValue = ceil($maxValue * 1.1); // Tambahkan 10% padding

        // Gambar garis grid dan label sumbu Y
        $numGridLines = 5;
        for ($i = 0; $i <= $numGridLines; $i++) {
            $y = $marginTop + $chartHeight - ($i * ($chartHeight / $numGridLines));
            $value = round(($i / $numGridLines) * $maxValue);

            // Garis grid horizontal
            imageline($image, $marginLeft, $y, $width - $marginRight, $y, $gridColor);

            // Label sumbu Y
            $labelWidth = strlen($value) * imagefontwidth(2);
            imagestring($image, 2, $marginLeft - $labelWidth - 5, $y - 7, $value, $textColor);
        }

        // Gambar border area grafik
        imagerectangle(
            $image,
            $marginLeft,
            $marginTop,
            $width - $marginRight,
            $height - $marginBottom,
            $borderColor
        );

        // Hitung lebar batang dan spasi
        $barCount = 4;
        $barWidth = ($chartWidth / $barCount) * 0.6; // 60% lebar tersedia per batang
        $barSpacing = ($chartWidth / $barCount) * 0.4; // 40% untuk spasi

        // Gambar batang untuk setiap nilai
        for ($i = 1; $i <= 4; $i++) {
            // Tinggi batang proporsional terhadap nilai
            $barHeight = ($stats[$i] / $maxValue) * $chartHeight;
            if ($barHeight < 2) $barHeight = 2; // Pastikan batang selalu terlihat

            // Posisi batang
            $barX = $marginLeft + ($i - 1) * ($barWidth + $barSpacing) + ($barSpacing / 2);
            $barY = $height - $marginBottom - $barHeight;

            // Gambar batang dengan efek gradient sederhana
            $colorLight = $colors[$i];
            $colorDark = imagecolorallocate(
                $image,
                max(0, imagecolorsforindex($image, $colors[$i])['red'] - 40),
                max(0, imagecolorsforindex($image, $colors[$i])['green'] - 40),
                max(0, imagecolorsforindex($image, $colors[$i])['blue'] - 40)
            );

            // Gambar batang dengan 3D effect sederhana
            imagefilledrectangle($image, $barX, $barY, $barX + $barWidth, $height - $marginBottom, $colorLight);
            imagefilledrectangle($image, $barX + 3, $barY + 3, $barX + $barWidth, $height - $marginBottom, $colorDark);
            imagerectangle($image, $barX, $barY, $barX + $barWidth, $height - $marginBottom, $borderColor);

            // Tambahkan label batang
            $labels = ["Kurang", "Cukup", "Baik", "Sangat Baik"];
            $label = $labels[$i - 1];

            // Center label
            $labelWidth = strlen($label) * imagefontwidth(3);
            $labelX = $barX + ($barWidth / 2) - ($labelWidth / 2);
            imagestring($image, 3, $labelX, $height - $marginBottom + 10, $label, $textColor);

            // Tambahkan nilai diatas batang
            $valueText = $stats[$i];
            $valueWidth = strlen($valueText) * imagefontwidth(4);
            $valueX = $barX + ($barWidth / 2) - ($valueWidth / 2);
            imagestring($image, 4, $valueX, $barY - 20, $valueText, $textColor);

            // Tambahkan persentase jika ada total
            if ($total > 0) {
                $percent = number_format(($stats[$i] / $total) * 100, 1) . "%";
                $percentWidth = strlen($percent) * imagefontwidth(3);
                $percentX = $barX + ($barWidth / 2) - ($percentWidth / 2);
                imagestring($image, 3, $percentX, $barY - 35, $percent, $textColor);
            }
        }

        // Tambahkan informasi ringkasan di bawah
        $avgScore = 0;
        if ($total > 0) {
            $avgScore = ($stats[1] * 1 + $stats[2] * 2 + $stats[3] * 3 + $stats[4] * 4) / $total;
        }

        // Gambar kotak info
        $infoBoxColor = imagecolorallocate($image, 245, 245, 245);
        $infoBoxX1 = $marginLeft;
        $infoBoxY1 = $height - 75;
        $infoBoxX2 = $width - $marginRight;
        $infoBoxY2 = $height - 15;

        imagefilledrectangle($image, $infoBoxX1, $infoBoxY1, $infoBoxX2, $infoBoxY2, $infoBoxColor);
        imagerectangle($image, $infoBoxX1, $infoBoxY1, $infoBoxX2, $infoBoxY2, $borderColor);

        // Tambahkan teks informasi ringkasan
        imagestring($image, 4, $infoBoxX1 + 15, $infoBoxY1 + 10, "Jumlah Responden: " . $total, $textColor);
        imagestring($image, 4, $infoBoxX1 + 15, $infoBoxY1 + 30, "Skor Rata-rata: " . number_format($avgScore, 2), $textColor);

        // Tambahkan keterangan bobot
        imagestring($image, 2, $width - 250, $infoBoxY1 + 10, "Kurang (1), Cukup (2), Baik (3), Sangat Baik (4)", $textColor);

        // Pastikan folder charts ada
        $chartDir = public_path('charts');
        if (!File::exists($chartDir)) {
            File::makeDirectory($chartDir, 0755, true);
        }

        // Buat nama file yang aman
        $safeTitle = Str::slug(substr($title, 0, 40));
        $filename = 'chart-' . $safeTitle . '-' . time() . '.png';
        $imagePath = $chartDir . '/' . $filename;

        // Simpan gambar ke file
        imagepng($image, $imagePath);
        imagedestroy($image);

        // Return URL relatif untuk diakses di template
        return asset('charts/' . $filename);
    }

    public function print($id)
    {
        $questionnaire = Questionnaire::findOrFail($id);
        $responses = Response::where('questionnaire_id', $id)->get();

        // Cari data report dari database
        $report = Report::where('questionnaire_id', $id)->first();

        // Hitung jumlah responden unik
        $uniqueResponderCount = DB::table('questionnaire_user')
            ->where('questionnaire_id', $id)
            ->whereNotNull('submitted_at')
            ->count();
        $userIds = DB::table('questionnaire_user')
            ->where('questionnaire_id', $id)
            ->whereNotNull('submitted_at')
            ->pluck('user_id');
        $users = User::whereIn('id', $userIds)->get();

        // Grup pertanyaan berdasarkan kategori
        $categories = QuestionnaireCategory::all();
        $questions = Question::where('questionnaire_id', $id)
            ->orderBy('category_id')
            ->orderBy('order')
            ->get();

        // Inisialisasi array untuk menyimpan hasil
        $questionStats = [];
        $categoryResults = [];
        $categoryScores = [];

        // Proses data nyata dari database
        foreach ($questions as $question) {
            // Ambil jawaban dari tabel responses, bukan answers
            $answers = DB::table('responses')
                ->select('rating as value', DB::raw('count(*) as total'))
                ->where('question_id', $question->id)
                ->where('questionnaire_id', $id)
                ->groupBy('rating')
                ->get();

            $stats = [
                1 => 0, // Kurang
                2 => 0, // Cukup
                3 => 0, // Baik
                4 => 0, // Sangat Baik
            ];

            $total = 0;
            $sum = 0;

            // Isi stats dengan data dari database
            foreach ($answers as $answer) {
                $value = (int)$answer->value;
                if ($value >= 1 && $value <= 4) {
                    $stats[$value] = $answer->total;
                    $total += $answer->total;
                    $sum += $value * $answer->total;
                }
            }

            $average = $total > 0 ? $sum / $total : 0;

            // Simpan statistik
            $questionStats[$question->id] = [
                'stats' => $stats,
                'average' => $average,
                'total' => $total
            ];

            // Buat grafik dan deskripsi
            $chartData = [
                'title' => $question->question,
                'stats' => $stats,
                'total' => $total,
                'average' => $average,
                'image' => $this->generateChartImage($question->question, $stats, $total),
                'description' => $this->generateChartDescription($stats, $total, $average, $question->question)
            ];

            // Simpan hasil dalam array kategori
            if (!isset($categoryResults[$question->category_id])) {
                $categoryResults[$question->category_id] = [];
            }

            $categoryResults[$question->category_id][] = $chartData;
        }

        // Hitung rata-rata untuk setiap kategori
        $categoryStats = [];
        foreach ($categories as $category) {
            $categoryQuestions = $questions->where('category_id', $category->id);
            $totalAverage = 0;
            $questionCount = 0;

            foreach ($categoryQuestions as $question) {
                if (isset($questionStats[$question->id])) {
                    $totalAverage += $questionStats[$question->id]['average'];
                    $questionCount++;
                }
            }

            $categoryAverage = $questionCount > 0 ? $totalAverage / $questionCount : 0;

            $categoryStats[$category->id] = [
                'name' => $category->name,
                'average' => $categoryAverage,
                'questions' => $categoryQuestions
            ];

            // Simpan skor rata-rata kategori
            $categoryScores[$category->name] = number_format($categoryAverage, 2);
        }

        // Hitung rata-rata keseluruhan
        $overallAverage = $responses->average('rating') ?? 0;

        // Siapkan data untuk template
        $data = [
            'questionnaire' => $questionnaire,
            'responses' => $responses,
            'categories' => $categories,
            'questions' => $questions,
            'questionStats' => $questionStats,
            'categoryStats' => $categoryStats,
            'categoryResults' => $categoryResults,
            'overallAverage' => $overallAverage,
            'title' => $questionnaire->title,
            'survey_type' => $this->getSurveyType($questionnaire->type),
            'responden' => $this->getRespondenType($questionnaire->type),
            'responden_type' => $this->getRespondenType($questionnaire->type),
            'responden_count' => $uniqueResponderCount, // Gunakan jumlah responden unik
            'reliability_score' => $categoryScores['Keandalan (Reliability)'] ?? '0.00',
            'responsiveness_score' => $categoryScores['Daya Tanggap (Responsiveness)'] ?? '0.00',
            'assurance_score' => $categoryScores['Kepastian (Assurance)'] ?? '0.00',
            'empathy_score' => $categoryScores['Empati (Empathy)'] ?? '0.00',
            'tangible_score' => $categoryScores['Sarana (Tangible)'] ?? '0.00',
            'average_score' => number_format($overallAverage, 2),
            'dekan_name' => 'Prof. Dr. Ir. Supriyanto, ST., M.Sc., IPM',
            'lampiran_images' => $this->getLampiranImages(),
            'users' => $users,
            'analisa_pembahasan' => $report->analysis_content ?? '...',
            'simpulan' => $report->conclusion_content ?? '...',
            'rtl' => $report->followup_content ?? '...',
        ];

        // Tambahkan konten khusus
        $specificContent = $this->getSpecificContent($questionnaire->type);
        $data = array_merge($data, $specificContent);

        // Tentukan pakai portrait atau landscape berdasarkan parameter
        $orientation = request('orientation', 'portrait');

        if ($orientation === 'landscape') {
            // Load view landscape
            $pdf = PDF::loadView('admin.reports.print_landscape', $data);
            $pdf->setPaper('a4', 'landscape');
            $pdf->setOption('page-size', 'A4');
            $pdf->setOption('orientation', 'landscape');
            $pdf->setOption('margin-top', 10);
            $pdf->setOption('margin-right', 10);
            $pdf->setOption('margin-bottom', 10);
            $pdf->setOption('margin-left', 10);

            return $pdf->stream('lampiran-' . strtolower(str_replace(' ', '-', $questionnaire->title)) . '.pdf');
        } else {
            // Load view portrait
            $pdf = PDF::loadView('pimpinan.reports.print', $data);
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption('margin-top', 25);
            $pdf->setOption('margin-right', 20);
            $pdf->setOption('margin-bottom', 25);
            $pdf->setOption('margin-left', 20);
            $pdf->setOption('footer-right', 'Halaman [page] dari [topage]');

            return $pdf->stream('laporan-' . strtolower(str_replace(' ', '-', $questionnaire->title)) . '.pdf');
        }
    }

    private function generateChartDescription($stats, $total, $average, $questionText)
    {
        if ($total == 0) {
            return "Belum ada responden yang menjawab pertanyaan ini.";
        }

        // Hitung persentase untuk setiap kategori
        $percentages = [
            1 => round(($stats[1] / $total) * 100, 1),
            2 => round(($stats[2] / $total) * 100, 1),
            3 => round(($stats[3] / $total) * 100, 1),
            4 => round(($stats[4] / $total) * 100, 1),
        ];

        // Template kategori
        $categoryNames = [
            1 => 'kurang',
            2 => 'cukup',
            3 => 'baik',
            4 => 'sangat baik'
        ];

        // Template penilaian berdasarkan rata-rata
        $assessment = '';
        if ($average >= 3.5) {
            $assessment = 'sangat baik';
        } elseif ($average >= 2.5) {
            $assessment = 'baik';
        } elseif ($average >= 1.5) {
            $assessment = 'cukup';
        } else {
            $assessment = 'kurang';
        }

        // Generate deskripsi berdasarkan data dengan menyertakan pertanyaan
        $description = "Untuk pertanyaan \"" . $questionText . "\", berdasarkan hasil survey dengan total {$total} responden, ";

        // Buat daftar semua kategori dengan distribusinya
        $distributions = [];
        foreach ([4, 3, 2, 1] as $category) { // Urutkan dari sangat baik ke kurang
            if ($stats[$category] > 0) {
                $distributions[] = "{$stats[$category]} responden ({$percentages[$category]}%) memberikan penilaian '{$categoryNames[$category]}'";
            } else {
                $distributions[] = "tidak ada responden yang memberikan penilaian '{$categoryNames[$category]}'";
            }
        }

        // Gabungkan distribusi dengan format yang baik
        if (count($distributions) > 1) {
            $lastDistribution = array_pop($distributions);
            $description .= implode(', ', $distributions) . ', dan ' . $lastDistribution . '. ';
        } else {
            $description .= $distributions[0] . '. ';
        }

        // Tambahkan informasi rata-rata dan kesimpulan
        $description .= "Dengan skor rata-rata " . number_format($average, 2) . " dari skala 1-4, ";
        $description .= "hal ini menunjukkan bahwa secara keseluruhan responden memberikan penilaian '{$assessment}' terhadap aspek ini. ";

        // Tambahkan rekomendasi berdasarkan hasil
        if ($average >= 3.5) {
            $description .= "Hasil ini mencerminkan tingkat kepuasan yang tinggi dan perlu dipertahankan.";
        } elseif ($average >= 2.5) {
            $description .= "Meskipun hasil sudah cukup memuaskan, masih terdapat ruang untuk peningkatan kualitas.";
        } else {
            $description .= "Hasil ini mengindikasikan perlunya perbaikan dan peningkatan kualitas pada aspek ini.";
        }

        return $description;
    }
}
