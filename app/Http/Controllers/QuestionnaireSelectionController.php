<?php

namespace App\Http\Controllers;

use App\Models\AcademicPeriod;
use App\Models\Questionnaire;
use Illuminate\Support\Facades\Auth;

class QuestionnaireSelectionController extends Controller
{
    public function index()
    {
        $activePeriod = AcademicPeriod::where('is_active', true)->first();

        if (!$activePeriod) {
            return redirect()->route('dashboard')->with('error', 'Tidak ada periode akademik aktif saat ini.');
        }

        $role = Auth::user()->role;

        // Dapatkan kuesioner yang tersedia berdasarkan role pengguna
        $questionnaires = Questionnaire::where('academic_period_id', $activePeriod->id)
            ->where('is_active', true)
            ->whereHas('permissions', function ($query) use ($role) {
                $query->where('role', $role)
                    ->where('can_fill', true);
            })
            ->get();

        // Dapatkan kuesioner yang sudah diisi oleh pengguna
        $completedQuestionnaires = Auth::user()->questionnaires()
            ->wherePivotNotNull('submitted_at')
            ->where('academic_period_id', $activePeriod->id)
            ->pluck('questionnaire_id')
            ->toArray();

        return view('questionnaire.selection', compact('questionnaires', 'completedQuestionnaires', 'activePeriod'));
    }
}
