<?php


namespace App\Http\Controllers;

use App\Models\Questionnaire;
use App\Models\Question;
use App\Models\Response;
use App\Models\Suggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResponseController extends Controller
{
    public function fill(Questionnaire $questionnaire)
    {
        // Cek apakah pengguna sudah mengisi kuesioner ini
        $user = Auth::user();
        $hasSubmitted = $questionnaire->users()->where('user_id', $user->id)->exists();

        if ($hasSubmitted) {
            return redirect()->route('questionnaires.select')
                ->with('error', 'Anda sudah mengisi kuesioner ini.');
        }

        // Cek apakah pengguna memiliki hak akses untuk mengisi kuesioner ini
        $hasPermission = $questionnaire->permissions()
            ->where('role', $user->role)
            ->where('can_fill', true)
            ->exists();

        if (!$hasPermission) {
            return redirect()->route('questionnaires.select')
                ->with('error', 'Anda tidak memiliki hak akses untuk mengisi kuesioner ini.');
        }

        // Ambil semua pertanyaan yang aktif dari kuesioner ini
        $questions = $questionnaire->questions()
            ->where('is_active', true)
            ->orderBy('category_id')
            ->orderBy('order')
            ->get();

        // Kelompokkan pertanyaan berdasarkan kategori
        $questionsByCategory = $questions->groupBy('category_id');

        // Ambil semua kategori
        $categories = \App\Models\QuestionnaireCategory::all()->keyBy('id');

        return view('questionnaire.fill', compact('questionnaire', 'questionsByCategory', 'categories'));
    }

    public function store(Request $request, Questionnaire $questionnaire)
    {
        $user = Auth::user();

        // Validasi input
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'ratings.*' => 'required|integer|min:1|max:4',
            'comments.*' => 'nullable|string|max:1000',
            'suggestion' => 'nullable|string|max:2000',
        ], [
            'ratings.*.required' => 'Semua pertanyaan harus diisi.',
            'ratings.*.integer' => 'Rating harus berupa angka.',
            'ratings.*.min' => 'Rating minimal adalah 1.',
            'ratings.*.max' => 'Rating maksimal adalah 4.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            // Simpan respon untuk setiap pertanyaan
            foreach ($request->ratings as $questionId => $rating) {
                $comment = null;

                // Cek apakah ada komentar untuk pertanyaan ini
                if (isset($request->comments[$questionId]) && !empty($request->comments[$questionId])) {
                    $comment = $request->comments[$questionId];
                }

                Response::create([
                    'questionnaire_id' => $questionnaire->id,
                    'question_id' => $questionId,
                    'user_id' => $user->id,
                    'rating' => $rating,
                    'comment' => $comment, // FIX: Pastikan ini tidak terputus
                ]);
            }

            // Catat bahwa pengguna telah mengisi kuesioner ini
            $questionnaire->users()->attach($user->id, ['submitted_at' => now()]);

            // Simpan saran jika ada
            if ($request->filled('suggestion')) {
                Suggestion::create([
                    'user_id' => $user->id,
                    'questionnaire_id' => $questionnaire->id,
                    'content' => $request->suggestion,
                    'status' => 'submitted',
                ]);
            }

            DB::commit();

            return redirect()->route('dashboard')
                ->with('success', 'Terima kasih! Kuesioner berhasil diisi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function results(Request $request, Questionnaire $questionnaire)
    {
        // Validasi akses
        $user = Auth::user();

        // Cek apakah user memiliki permission untuk melihat hasil
        $canViewResults = $questionnaire->permissions()
            ->where('role', $user->role)
            ->where('can_view_results', true)
            ->exists();

        if (!$canViewResults && !in_array($user->role, ['admin', 'pimpinan'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk melihat hasil kuesioner.');
        }

        // Load pertanyaan dengan kategori
        $questionnaire->load(['questions.category', 'academicPeriod']);

        // Hitung rata-rata per kategori
        $categoryAverages = DB::table('responses')
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
        $questionAverages = DB::table('responses')
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
            ->get();

        // Jumlah responden per kategori
        $respondentsByRole = DB::table('questionnaire_user')
            ->join('users', 'questionnaire_user.user_id', '=', 'users.id')
            ->where('questionnaire_user.questionnaire_id', $questionnaire->id)
            ->whereNotNull('questionnaire_user.submitted_at')
            ->select('users.role', DB::raw('COUNT(*) as count'))
            ->groupBy('users.role')
            ->get();

        // Komposisi responden (pie chart)
        $respondentComposition = [
            'labels' => $respondentsByRole->pluck('role')->toArray(),
            'data' => $respondentsByRole->pluck('count')->toArray()
        ];

        // Data untuk chart kategori (radar chart)
        $categoryData = [
            'labels' => $categoryAverages->pluck('category')->toArray(),
            'data' => $categoryAverages->pluck('average_rating')->toArray()
        ];

        return view('responses.results', compact(
            'questionnaire',
            'categoryAverages',
            'questionAverages',
            'averageTotal',
            'totalRespondents',
            'ratingDistribution',
            'respondentsByRole',
            'respondentComposition',
            'categoryData'
        ));
    }

    /**
     * Menampilkan riwayat kuesioner yang telah diisi oleh pengguna
     */
    public function history()
    {
        // Ambil kuesioner yang sudah diisi oleh pengguna saat ini
        $completedQuestionnaires = Auth::user()->questionnaires()
            ->wherePivotNotNull('submitted_at')
            ->with('academicPeriod')
            ->orderBy('questionnaire_user.submitted_at', 'desc')
            ->get();

        // Ambil semua response (jawaban) yang diberikan oleh pengguna
        $userResponses = Response::where('user_id', Auth::id())
            ->with('question.category', 'questionnaire')
            ->get()
            ->groupBy('questionnaire_id');

        return view('responses.history', compact('completedQuestionnaires', 'userResponses'));
    }

    public function index()
    {
        // Ambil kuesioner yang sudah diisi oleh pengguna saat ini
        $completedQuestionnaires = Auth::user()->questionnaires()
            ->wherePivotNotNull('submitted_at')
            ->with('academicPeriod')
            ->orderBy('questionnaire_user.submitted_at', 'desc')
            ->get();

        // Ambil semua response (jawaban) yang diberikan oleh pengguna
        $userResponses = Response::where('user_id', Auth::id())
            ->with('question.category', 'questionnaire')
            ->get()
            ->groupBy('questionnaire_id');

        return view('responses.history', compact('completedQuestionnaires', 'userResponses'));
    }

    /**
     * Menampilkan detail riwayat kuesioner yang sudah diisi
     */
    public function historyDetail(Questionnaire $questionnaire)
    {
        // Pastikan pengguna telah mengisi kuesioner ini
        $hasSubmitted = Auth::user()->questionnaires()
            ->wherePivot('questionnaire_id', $questionnaire->id)
            ->wherePivotNotNull('submitted_at')
            ->exists();

        if (!$hasSubmitted) {
            return redirect()->route('responses.history')
                ->with('error', 'Anda belum pernah mengisi kuesioner ini.');
        }

        // Ambil semua pertanyaan beserta jawaban pengguna
        $questionnaire->load(['questions.category', 'academicPeriod']);
        $responses = Response::where('questionnaire_id', $questionnaire->id)
            ->where('user_id', Auth::id())
            ->get()
            ->keyBy('question_id');

        // Kelompokkan pertanyaan berdasarkan kategori
        $questionsByCategory = $questionnaire->questions->groupBy(function ($question) {
            return $question->category->name;
        });

        // Ambil saran yang diberikan jika ada
        $suggestion = \App\Models\Suggestion::where('questionnaire_id', $questionnaire->id)
            ->where('user_id', Auth::id())
            ->first();

        // Ambil waktu pengisian
        $submittedAt = Auth::user()->questionnaires()
            ->where('questionnaire_id', $questionnaire->id)
            ->first()
            ->pivot
            ->submitted_at;

        return view('responses.history-detail', compact(
            'questionnaire',
            'questionsByCategory',
            'responses',
            'suggestion',
            'submittedAt'
        ));
    }
}
