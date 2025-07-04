<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnairePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminQuestionnaireController extends Controller
{
    public function index(Request $request)
    {
        // Mendapatkan semua periode akademik untuk dropdown filter
        $academicPeriods = AcademicPeriod::orderBy('year', 'desc')->orderBy('semester', 'desc')->get();

        // Mendapatkan periode yang dipilih atau default ke periode aktif
        $selectedPeriodId = $request->input('academic_period_id', null);

        if ($selectedPeriodId) {
            $activePeriod = AcademicPeriod::findOrFail($selectedPeriodId);
        } else {
            $activePeriod = AcademicPeriod::where('is_active', true)->first();
            $selectedPeriodId = $activePeriod ? $activePeriod->id : null;
        }

        // Mendapatkan kuesioner untuk periode yang dipilih
        $query = Questionnaire::with('academicPeriod', 'permissions');

        if ($selectedPeriodId) {
            $query->where('academic_period_id', $selectedPeriodId);
        }

        $questionnaires = $query->orderBy('questionnaires.id', 'desc')->get();

        return view('admin.questionnaires.index', compact('questionnaires', 'academicPeriods', 'activePeriod', 'selectedPeriodId'));
    }

    public function create()
    {
        // Mendapatkan periode aktif dan kategori untuk form pembuatan
        $academicPeriods = AcademicPeriod::where('is_active', true)->get();
        $categories = QuestionnaireCategory::all();
        $roles = ['mahasiswa', 'dosen', 'tendik', 'alumni', 'pengguna_lulusan', 'mitra'];

        // Mendapatkan daftar kuesioner untuk opsi template
        $previousQuestionnaires = Questionnaire::orderBy('created_at', 'desc')->get();

        return view('admin.questionnaires.create', compact('academicPeriods', 'categories', 'roles', 'previousQuestionnaires'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:255',
            'academic_period_id' => 'required|exists:academic_periods,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'respondent_roles' => 'required|array',
            'respondent_roles.*' => 'required|string|in:mahasiswa,dosen,tendik,alumni,pengguna_lulusan,mitra',
            'template_questionnaire_id' => 'nullable|exists:questionnaires,id',
        ]);

        // Membuat slug dari judul
        $slug = Str::slug($request->title);

        // Memastikan slug unik
        $count = 1;
        $originalSlug = $slug;
        while (Questionnaire::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        // Membuat kuesioner baru
        $questionnaire = Questionnaire::create([
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'type' => $request->type,
            'academic_period_id' => $request->academic_period_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
        ]);

        // Membuat izin untuk role yang dipilih
        foreach ($request->respondent_roles as $role) {
            QuestionnairePermission::create([
                'questionnaire_id' => $questionnaire->id,
                'role' => $role,
                'can_fill' => true,
                'can_view_results' => false,
            ]);
        }

        // Selalu membuat izin untuk admin dan pimpinan
        QuestionnairePermission::create([
            'questionnaire_id' => $questionnaire->id,
            'role' => 'admin',
            'can_fill' => false,
            'can_view_results' => true,
        ]);

        QuestionnairePermission::create([
            'questionnaire_id' => $questionnaire->id,
            'role' => 'pimpinan',
            'can_fill' => false,
            'can_view_results' => true,
        ]);

        // Jika menggunakan template, salin semua pertanyaan dari kuesioner template
        if ($request->has('template_questionnaire_id') && $request->template_questionnaire_id) {
            $templateQuestionnaire = Questionnaire::findOrFail($request->template_questionnaire_id);
            $templateQuestions = $templateQuestionnaire->questions()->orderBy('order')->get();

            foreach ($templateQuestions as $templateQuestion) {
                Question::create([
                    'questionnaire_id' => $questionnaire->id,
                    'category_id' => $templateQuestion->category_id,
                    'question' => $templateQuestion->question,
                    'order' => $templateQuestion->order,
                    'is_required' => $templateQuestion->is_required,
                    'is_active' => $templateQuestion->is_active,
                ]);
            }

            return redirect()->route('admin.questionnaires.show', $questionnaire)
                ->with('success', 'Kuesioner berhasil dibuat dengan menggunakan template. Semua pertanyaan telah disalin.');
        }

        return redirect()->route('admin.questionnaires.show', $questionnaire)
            ->with('success', 'Kuesioner berhasil dibuat. Silakan tambahkan pertanyaan.');
    }

    public function show(Questionnaire $questionnaire)
    {
        // Memuat relasi untuk tampilan detail
        $questionnaire->load('academicPeriod', 'permissions', 'questions.category');
        $categories = QuestionnaireCategory::all();

        return view('admin.questionnaires.show', compact('questionnaire', 'categories'));
    }

    public function edit(Questionnaire $questionnaire)
    {
        // Menyiapkan data untuk form edit
        $academicPeriods = AcademicPeriod::all();
        $roles = ['mahasiswa', 'dosen', 'tendik', 'alumni', 'pengguna_lulusan', 'mitra'];
        $selectedRoles = $questionnaire->permissions()
            ->where('can_fill', true)
            ->pluck('role')
            ->toArray();

        return view('admin.questionnaires.edit', compact('questionnaire', 'academicPeriods', 'roles', 'selectedRoles'));
    }

    public function update(Request $request, Questionnaire $questionnaire)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:255',
            'academic_period_id' => 'required|exists:academic_periods,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'nullable|boolean',
            'respondent_roles' => 'required|array',
            'respondent_roles.*' => 'required|string|in:mahasiswa,dosen,tendik,alumni,pengguna_lulusan,mitra',
        ]);

        // Update slug jika judul berubah
        if ($questionnaire->title != $request->title) {
            $slug = Str::slug($request->title);

            // Memastikan slug unik
            $count = 1;
            $originalSlug = $slug;
            while (Questionnaire::where('slug', $slug)->where('id', '!=', $questionnaire->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            $questionnaire->slug = $slug;
        }

        // Update kuesioner
        $questionnaire->title = $request->title;
        $questionnaire->description = $request->description;
        $questionnaire->type = $request->type;
        $questionnaire->academic_period_id = $request->academic_period_id;
        $questionnaire->start_date = $request->start_date;
        $questionnaire->end_date = $request->end_date;
        if ($request->has('is_active')) {
            $questionnaire->is_active = $request->boolean('is_active');
        }
        $questionnaire->save();

        // Update izin
        // Hapus izin yang ada yang mengizinkan pengisian (kecuali admin dan pimpinan)
        $questionnaire->permissions()
            ->whereNotIn('role', ['admin', 'pimpinan'])
            ->delete();

        // Buat izin baru untuk role yang dipilih
        foreach ($request->respondent_roles as $role) {
            QuestionnairePermission::create([
                'questionnaire_id' => $questionnaire->id,
                'role' => $role,
                'can_fill' => true,
                'can_view_results' => false,
            ]);
        }

        return redirect()->route('admin.questionnaires.index')
            ->with('success', 'Kuesioner berhasil diperbarui.');
    }

    public function destroy(Questionnaire $questionnaire)
    {
        // Hapus pertanyaan dan izin terkait
        $questionnaire->questions()->delete();
        $questionnaire->permissions()->delete();

        // Hapus kuesioner
        $questionnaire->delete();

        return redirect()->route('admin.questionnaires.index')
            ->with('success', 'Kuesioner berhasil dihapus.');
    }

    public function storeQuestion(Request $request, Questionnaire $questionnaire)
    {
        $request->validate([
            'category_id' => 'required|exists:questionnaire_categories,id',
            'question' => 'required|string',
            'is_required' => 'boolean',
        ]);

        // Mendapatkan urutan tertinggi untuk kuesioner ini
        $highestOrder = Question::where('questionnaire_id', $questionnaire->id)->max('order') ?? 0;

        // Membuat pertanyaan baru
        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'category_id' => $request->category_id,
            'question' => $request->question,
            'order' => $highestOrder + 1,
            'is_required' => $request->has('is_required'),
            'is_active' => true,
        ]);

        return redirect()->route('admin.questionnaires.show', $questionnaire)
            ->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function updateQuestion(Request $request, Question $question)
    {
        $request->validate([
            'category_id' => 'required|exists:questionnaire_categories,id',
            'question' => 'required|string',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Update pertanyaan
        $question->update([
            'category_id' => $request->category_id,
            'question' => $request->question,
            'is_required' => $request->has('is_required'),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.questionnaires.show', $question->questionnaire)
            ->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function destroyQuestion(Question $question)
    {
        $questionnaire = $question->questionnaire;

        // Hapus pertanyaan
        $question->delete();

        // Mengurutkan kembali pertanyaan yang tersisa
        $remainingQuestions = Question::where('questionnaire_id', $questionnaire->id)
            ->orderBy('order')
            ->get();

        foreach ($remainingQuestions as $index => $q) {
            $q->update(['order' => $index + 1]);
        }

        return redirect()->route('admin.questionnaires.show', $questionnaire)
            ->with('success', 'Pertanyaan berhasil dihapus.');
    }

    public function updateQuestionOrder(Request $request, Questionnaire $questionnaire)
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*' => 'required|exists:questions,id',
        ]);

        // Update urutan pertanyaan
        foreach ($request->questions as $index => $id) {
            Question::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    public function duplicate(Request $request, Questionnaire $questionnaire)
    {
        $request->validate([
            'academic_period_id' => 'required|exists:academic_periods,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Membuat slug dari judul
        $newTitle = $questionnaire->title . ' (Salinan)';
        $slug = Str::slug($newTitle);

        // Memastikan slug unik
        $count = 1;
        $originalSlug = $slug;
        while (Questionnaire::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        // Membuat kuesioner baru berdasarkan kuesioner yang ada
        $newQuestionnaire = Questionnaire::create([
            'title' => $newTitle,
            'slug' => $slug,
            'description' => $questionnaire->description,
            'type' => $questionnaire->type,
            'academic_period_id' => $request->academic_period_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
        ]);

        // Salin izin dari kuesioner yang ada
        $permissions = $questionnaire->permissions()->get();
        foreach ($permissions as $permission) {
            QuestionnairePermission::create([
                'questionnaire_id' => $newQuestionnaire->id,
                'role' => $permission->role,
                'can_fill' => $permission->can_fill,
                'can_view_results' => $permission->can_view_results,
            ]);
        }

        // Salin pertanyaan dari kuesioner yang ada
        $questions = $questionnaire->questions()->orderBy('order')->get();
        foreach ($questions as $question) {
            Question::create([
                'questionnaire_id' => $newQuestionnaire->id,
                'category_id' => $question->category_id,
                'question' => $question->question,
                'order' => $question->order,
                'is_required' => $question->is_required,
                'is_active' => $question->is_active,
            ]);
        }

        return redirect()->route('admin.questionnaires.show', $newQuestionnaire)
            ->with('success', 'Kuesioner berhasil diduplikasi dengan semua pertanyaan.');
    }
}
