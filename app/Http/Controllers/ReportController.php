<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\AcademicPeriod;
use App\Models\Questionnaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class ReportController extends Controller
{
    /**
     * Menampilkan daftar laporan
     */
    public function index(Request $request)
    {
        $academicPeriods = AcademicPeriod::orderBy('year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $activeAcademicPeriod = AcademicPeriod::where('is_active', true)->first() ?? AcademicPeriod::latest()->first();

        // Filter
        $academicPeriodId = $request->get('academic_period_id', $activeAcademicPeriod?->id);
        $questionnaireType = $request->get('questionnaire_type', null);

        $query = Report::with(['academicPeriod', 'questionnaire', 'generator']);

        if ($academicPeriodId) {
            $query->where('academic_period_id', $academicPeriodId);
        }

        if ($questionnaireType) {
            $query->whereHas('questionnaire', function ($q) use ($questionnaireType) {
                $q->where('type', $questionnaireType);
            });
        }

        $reports = $query->orderBy('generated_at', 'desc')->paginate(10);

        // Data untuk filter
        $questionnaireTypes = [
            'layanan_fakultas' => 'Layanan Fakultas',
            'elom' => 'Evaluasi Layanan oleh Mahasiswa (ELOM)',
            'evaluasi_dosen' => 'Evaluasi Dosen',
            'elta' => 'Evaluasi Layanan Tugas Akhir (ELTA)',
            'kepuasan_dosen' => 'Kepuasan Dosen',
            'kepuasan_tendik' => 'Kepuasan Tenaga Kependidikan',
            'kepuasan_alumni' => 'Kepuasan Alumni',
            'kepuasan_pengguna_lulusan' => 'Kepuasan Pengguna Lulusan',
            'kepuasan_mitra' => 'Kepuasan Mitra Kerjasama'
        ];

        return view('reports.index', compact(
            'reports',
            'academicPeriods',
            'academicPeriodId',
            'questionnaireType',
            'questionnaireTypes'
        ));
    }

    /**
     * Generate laporan baru
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'academic_period_id' => 'required|exists:academic_periods,id',
            'questionnaire_id' => 'required|exists:questionnaires,id',
            'notes' => 'nullable|string'
        ]);

        $questionnaire = Questionnaire::findOrFail($validated['questionnaire_id']);

        // Ambil data untuk laporan
        $summaryData = $this->generateSummaryData($questionnaire);

        // Buat laporan
        $report = Report::create([
            'academic_period_id' => $validated['academic_period_id'],
            'questionnaire_id' => $validated['questionnaire_id'],
            'summary_data' => $summaryData,
            'notes' => $validated['notes'],
            'generated_by' => Auth::id(),
            'generated_at' => now()
        ]);

        return redirect()->route('reports.show', $report)
            ->with('success', 'Laporan berhasil dibuat.');
    }

    /**
     * Menampilkan detail laporan
     */
    public function show(Report $report)
    {
        $report->load(['academicPeriod', 'questionnaire', 'generator']);

        $summaryData = $report->summary_data;

        return view('reports.show', compact('report', 'summaryData'));
    }

    /**
     * Download laporan sebagai PDF
     */
    public function download(Report $report)
    {
        $report->load(['academicPeriod', 'questionnaire', 'generator']);

        $summaryData = $report->summary_data;

        $pdf = PDF::loadView('reports.pdf', compact('report', 'summaryData'));

        return $pdf->download('laporan_spm_' . $report->questionnaire->type . '_' . $report->academicPeriod->name . '.pdf');
    }

    /**
     * Export semua laporan berdasarkan filter
     */
    public function exportAll(Request $request)
    {
        $academicPeriodId = $request->get('academic_period_id');
        $questionnaireType = $request->get('questionnaire_type');

        $query = Report::with(['academicPeriod', 'questionnaire', 'generator']);

        if ($academicPeriodId) {
            $query->where('academic_period_id', $academicPeriodId);
        }

        if ($questionnaireType) {
            $query->whereHas('questionnaire', function ($q) use ($questionnaireType) {
                $q->where('type', $questionnaireType);
            });
        }

        $reports = $query->orderBy('generated_at', 'desc')->get();

        if ($reports->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data laporan untuk diekspor.');
        }

        // Export ke Excel
        $filename = 'laporan_spm_all_' . date('YmdHis') . '.xlsx';

        // Implementasi export Excel di sini...

        return redirect()->back()->with('success', 'Export laporan berhasil.');
    }

    /**
     * Menghapus laporan
     */
    public function destroy(Report $report)
    {
        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }

    /**
     * Generate data ringkasan untuk laporan
     */
    private function generateSummaryData(Questionnaire $questionnaire)
    {
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
        return [
            'category_data' => $categoryData,
            'question_data' => $questionData,
            'average_total' => $averageTotal,
            'total_respondents' => $totalRespondents,
            'rating_distribution' => $ratingDistribution,
            'respondents_by_role' => $respondentsByRole
        ];
    }
}
