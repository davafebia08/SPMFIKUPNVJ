<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\Questionnaire;
use App\Models\Question;
use App\Models\QuestionnairePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminScheduleController extends Controller
{
    public function index()
    {
        // Dapatkan semua periode akademik yang diurutkan berdasarkan tahun dan semester
        $academicPeriods = AcademicPeriod::orderBy('year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        // Dapatkan periode yang memiliki kuesioner untuk dropdown copy
        $periodsWithQuestionnaires = AcademicPeriod::whereHas('questionnaires')
            ->withCount('questionnaires')
            ->orderBy('year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        return view('admin.schedules.index', compact('academicPeriods', 'periodsWithQuestionnaires'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'semester' => 'required|string|in:Ganjil,Genap',
            'year' => 'required|string|max:10',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'nullable|boolean',
            'copy_questionnaires' => 'nullable|boolean',
            'source_period_id' => 'nullable|exists:academic_periods,id'
        ]);

        // Jika periode baru diaktifkan, nonaktifkan periode lain
        if ($request->has('is_active')) {
            AcademicPeriod::where('is_active', true)->update(['is_active' => false]);
        }

        // Buat periode baru
        $newPeriod = AcademicPeriod::create([
            'name' => $request->name,
            'semester' => $request->semester,
            'year' => $request->year,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active')
        ]);

        $message = 'Periode akademik berhasil ditambahkan.';

        // Jika ada request copy kuesioner
        if ($request->has('copy_questionnaires') && $request->source_period_id) {
            $sourcePeriod = AcademicPeriod::find($request->source_period_id);
            $this->copyQuestionnaires($sourcePeriod, $newPeriod);
            $message = "Periode akademik berhasil ditambahkan dengan kuesioner dari periode {$sourcePeriod->name}.";
        }

        return redirect()->route('admin.schedules.index')
            ->with('success', $message);
    }

    private function copyQuestionnaires(AcademicPeriod $sourcePeriod, AcademicPeriod $targetPeriod)
    {
        $questionnaires = $sourcePeriod->questionnaires()->with(['questions', 'permissions'])->get();

        foreach ($questionnaires as $questionnaire) {
            // Copy kuesioner
            $newQuestionnaire = Questionnaire::create([
                'title' => $questionnaire->title,
                'slug' => $questionnaire->slug . '-' . Str::random(5), // Buat slug unik
                'description' => $questionnaire->description,
                'type' => $questionnaire->type,
                'academic_period_id' => $targetPeriod->id,
                'start_date' => $targetPeriod->start_date,
                'end_date' => $targetPeriod->end_date,
                'is_active' => true
            ]);

            // Copy permissions (Kelompok Responden)
            foreach ($questionnaire->permissions as $permission) {
                \App\Models\QuestionnairePermission::create([
                    'questionnaire_id' => $newQuestionnaire->id,
                    'role' => $permission->role,
                    'can_fill' => $permission->can_fill,
                    'can_view_results' => $permission->can_view_results,
                ]);
            }

            // Copy semua pertanyaan
            foreach ($questionnaire->questions as $question) {
                Question::create([
                    'questionnaire_id' => $newQuestionnaire->id,
                    'category_id' => $question->category_id,
                    'question' => $question->question, // Sesuai dengan struktur di AdminQuestionnaireController
                    'order' => $question->order,
                    'is_required' => $question->is_required,
                    'is_active' => $question->is_active,
                ]);
            }
        }
    }

    public function update(Request $request, AcademicPeriod $academicPeriod)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'semester' => 'required|string|in:Ganjil,Genap',
            'year' => 'required|string|max:10',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        // Jika periode yang diedit diaktifkan, nonaktifkan periode lain
        if ($request->has('is_active') && !$academicPeriod->is_active) {
            AcademicPeriod::where('is_active', true)->update(['is_active' => false]);
        }

        $academicPeriod->update([
            'name' => $request->name,
            'semester' => $request->semester,
            'year' => $request->year,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Periode akademik berhasil diperbarui.');
    }

    public function setActive(AcademicPeriod $academicPeriod)
    {
        // Nonaktifkan semua periode
        AcademicPeriod::where('is_active', true)->update(['is_active' => false]);

        // Aktifkan periode yang dipilih
        $academicPeriod->update(['is_active' => true]);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Periode ' . $academicPeriod->name . ' berhasil diaktifkan.');
    }

    public function destroy(AcademicPeriod $academicPeriod)
    {
        // Periksa apakah ada kuesioner yang terkait dengan periode ini
        $hasQuestionnaires = Questionnaire::where('academic_period_id', $academicPeriod->id)->exists();

        if ($hasQuestionnaires) {
            return redirect()->route('admin.schedules.index')
                ->with('error', 'Periode akademik tidak dapat dihapus karena memiliki kuesioner terkait.');
        }

        // Periksa apakah periode ini sedang aktif
        if ($academicPeriod->is_active) {
            return redirect()->route('admin.schedules.index')
                ->with('error', 'Periode akademik aktif tidak dapat dihapus. Aktifkan periode lain terlebih dahulu.');
        }

        $academicPeriod->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Periode akademik berhasil dihapus.');
    }
}
