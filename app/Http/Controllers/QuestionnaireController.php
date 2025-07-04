<?php

namespace App\Http\Controllers;

use App\Models\Questionnaire;
use App\Models\Question;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnairePermission;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QuestionnaireController extends Controller
{
    public function index(Request $request)
    {
        $academicPeriods = AcademicPeriod::orderBy('year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $activeAcademicPeriod = AcademicPeriod::where('is_active', true)->first() ?? AcademicPeriod::latest()->first();

        // Filter
        $academicPeriodId = $request->get('academic_period_id', $activeAcademicPeriod?->id);
        $type = $request->get('type', null);
        $isActive = $request->get('is_active', null);

        $query = Questionnaire::query();

        if ($academicPeriodId) {
            $query->where('academic_period_id', $academicPeriodId);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        $questionnaires = $query->orderBy('created_at', 'desc')->paginate(10);

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

        return view('questionnaires.index', compact(
            'questionnaires',
            'academicPeriods',
            'academicPeriodId',
            'type',
            'isActive',
            'questionnaireTypes'
        ));
    }

    public function create()
    {
        $academicPeriods = AcademicPeriod::orderBy('year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $categories = QuestionnaireCategory::all();

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

        $roles = [
            'mahasiswa' => 'Mahasiswa',
            'dosen' => 'Dosen',
            'tendik' => 'Tenaga Kependidikan',
            'alumni' => 'Alumni',
            'pengguna_lulusan' => 'Pengguna Lulusan',
            'mitra' => 'Mitra Kerjasama'
        ];

        return view('questionnaires.create', compact(
            'academicPeriods',
            'categories',
            'questionnaireTypes',
            'roles'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:layanan_fakultas,elom,evaluasi_dosen,elta,kepuasan_dosen,kepuasan_tendik,kepuasan_alumni,kepuasan_pengguna_lulusan,kepuasan_mitra',
            'academic_period_id' => 'required|exists:academic_periods,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'required|boolean',
            'roles' => 'required|array',
            'roles.*' => 'in:mahasiswa,dosen,tendik,alumni,pengguna_lulusan,mitra',
            'questions' => 'required|array',
            'questions.*.question' => 'required|string',
            'questions.*.category_id' => 'required|exists:questionnaire_categories,id',
            'questions.*.is_required' => 'boolean',
            'questions.*.order' => 'integer'
        ]);

        // Buat slug dari judul
        $slug = Str::slug($validated['title']);

        // Buat kuesioner
        $questionnaire = Questionnaire::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'],
            'type' => $validated['type'],
            'academic_period_id' => $validated['academic_period_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $validated['is_active']
        ]);

        // Buat pertanyaan
        foreach ($validated['questions'] as $questionData) {
            Question::create([
                'questionnaire_id' => $questionnaire->id,
                'category_id' => $questionData['category_id'],
                'question' => $questionData['question'],
                'order' => $questionData['order'] ?? 0,
                'is_required' => $questionData['is_required'] ?? true,
                'is_active' => true
            ]);
        }

        // Set permission untuk roles yang bisa mengisi kuesioner
        foreach ($validated['roles'] as $role) {
            QuestionnairePermission::create([
                'questionnaire_id' => $questionnaire->id,
                'role' => $role,
                'can_fill' => true,
                'can_view_results' => false
            ]);
        }

        // Set permission untuk admin dan pimpinan
        QuestionnairePermission::create([
            'questionnaire_id' => $questionnaire->id,
            'role' => 'admin',
            'can_fill' => false,
            'can_view_results' => true
        ]);

        QuestionnairePermission::create([
            'questionnaire_id' => $questionnaire->id,
            'role' => 'pimpinan',
            'can_fill' => false,
            'can_view_results' => true
        ]);

        return redirect()->route('questionnaires.index')
            ->with('success', 'Kuesioner berhasil dibuat.');
    }

    public function show(Questionnaire $questionnaire)
    {
        $questionnaire->load(['questions.category', 'academicPeriod', 'permissions']);

        $questionsByCategory = $questionnaire->questions->groupBy(function ($question) {
            return $question->category->name;
        });

        return view('questionnaires.show', compact('questionnaire', 'questionsByCategory'));
    }

    public function edit(Questionnaire $questionnaire)
    {
        $questionnaire->load(['questions.category', 'academicPeriod', 'permissions']);

        $academicPeriods = AcademicPeriod::orderBy('year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $categories = QuestionnaireCategory::all();

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

        $roles = [
            'mahasiswa' => 'Mahasiswa',
            'dosen' => 'Dosen',
            'tendik' => 'Tenaga Kependidikan',
            'alumni' => 'Alumni',
            'pengguna_lulusan' => 'Pengguna Lulusan',
            'mitra' => 'Mitra Kerjasama'
        ];

        $selectedRoles = $questionnaire->permissions
            ->where('can_fill', true)
            ->whereNotIn('role', ['admin', 'pimpinan'])
            ->pluck('role')
            ->toArray();

        return view('questionnaires.edit', compact(
            'questionnaire',
            'academicPeriods',
            'categories',
            'questionnaireTypes',
            'roles',
            'selectedRoles'
        ));
    }

    public function update(Request $request, Questionnaire $questionnaire)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:layanan_fakultas,elom,evaluasi_dosen,elta,kepuasan_dosen,kepuasan_tendik,kepuasan_alumni,kepuasan_pengguna_lulusan,kepuasan_mitra',
            'academic_period_id' => 'required|exists:academic_periods,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'required|boolean',
            'roles' => 'required|array',
            'roles.*' => 'in:mahasiswa,dosen,tendik,alumni,pengguna_lulusan,mitra',
            'questions' => 'required|array',
            'questions.*.id' => 'nullable|exists:questions,id',
            'questions.*.question' => 'required|string',
            'questions.*.category_id' => 'required|exists:questionnaire_categories,id',
            'questions.*.is_required' => 'boolean',
            'questions.*.order' => 'integer',
            'questions.*.is_active' => 'boolean'
        ]);

        // Perbarui data kuesioner
        $questionnaire->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'academic_period_id' => $validated['academic_period_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $validated['is_active']
        ]);

        // Perbarui pertanyaan
        $existingQuestionIds = [];

        foreach ($validated['questions'] as $questionData) {
            if (isset($questionData['id'])) {
                // Update pertanyaan yang sudah ada
                $question = Question::find($questionData['id']);
                $question->update([
                    'category_id' => $questionData['category_id'],
                    'question' => $questionData['question'],
                    'order' => $questionData['order'] ?? 0,
                    'is_required' => $questionData['is_required'] ?? true,
                    'is_active' => $questionData['is_active'] ?? true
                ]);

                $existingQuestionIds[] = $question->id;
            } else {
                // Buat pertanyaan baru
                $question = Question::create([
                    'questionnaire_id' => $questionnaire->id,
                    'category_id' => $questionData['category_id'],
                    'question' => $questionData['question'],
                    'order' => $questionData['order'] ?? 0,
                    'is_required' => $questionData['is_required'] ?? true,
                    'is_active' => true
                ]);

                $existingQuestionIds[] = $question->id;
            }
        }

        // Hapus pertanyaan yang tidak ada dalam request
        Question::where('questionnaire_id', $questionnaire->id)
            ->whereNotIn('id', $existingQuestionIds)
            ->delete();

        // Perbarui permission
        QuestionnairePermission::where('questionnaire_id', $questionnaire->id)
            ->whereNotIn('role', ['admin', 'pimpinan'])
            ->delete();

        foreach ($validated['roles'] as $role) {
            QuestionnairePermission::create([
                'questionnaire_id' => $questionnaire->id,
                'role' => $role,
                'can_fill' => true,
                'can_view_results' => false
            ]);
        }

        return redirect()->route('questionnaires.index')
            ->with('success', 'Kuesioner berhasil diperbarui.');
    }

    public function destroy(Questionnaire $questionnaire)
    {
        // Hapus kuesioner (cascade delete akan menghapus pertanyaan, permission, dll)
        $questionnaire->delete();

        return redirect()->route('questionnaires.index')
            ->with('success', 'Kuesioner berhasil dihapus.');
    }
}
