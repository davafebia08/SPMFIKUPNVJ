<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Questionnaire;
use App\Models\QuestionnairePermission;
use Illuminate\Http\Request;

class AdminRespondentCategoryController extends Controller
{
    public function index()
    {
        // Daftar role yang tersedia beserta labelnya
        $roles = [
            'mahasiswa' => 'Mahasiswa',
            'dosen' => 'Dosen',
            'tendik' => 'Tenaga Kependidikan',
            'alumni' => 'Alumni',
            'pengguna_lulusan' => 'Pengguna Lulusan',
            'mitra' => 'Mitra'
        ];

        // Mengambil semua kuesioner yang aktif
        $questionnaires = Questionnaire::where('is_active', true)->get();

        // Menyiapkan array untuk menyimpan izin per role
        $rolePermissions = [];

        // Mengambil izin untuk setiap role
        foreach ($roles as $role => $label) {
            $permissions = QuestionnairePermission::where('role', $role)
                ->where('can_fill', true)
                ->pluck('questionnaire_id')
                ->toArray();

            $rolePermissions[$role] = [
                'permissions' => $permissions,
                'count' => count($permissions)
            ];
        }

        return view('admin.respondent-categories.index', compact('roles', 'questionnaires', 'rolePermissions'));
    }

    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'role' => 'required|string|in:mahasiswa,dosen,tendik,alumni,pengguna_lulusan,mitra',
            'questionnaire_ids' => 'required|array',
            'questionnaire_ids.*' => 'exists:questionnaires,id'
        ]);

        $role = $request->role;
        $questionnaireIds = $request->questionnaire_ids;

        // Menghapus semua izin yang ada untuk role ini
        QuestionnairePermission::where('role', $role)
            ->where('can_fill', true)
            ->delete();

        // Membuat izin baru untuk setiap kuesioner yang dipilih
        foreach ($questionnaireIds as $questionnaireId) {
            QuestionnairePermission::create([
                'questionnaire_id' => $questionnaireId,
                'role' => $role,
                'can_fill' => true,
                'can_view_results' => false
            ]);
        }

        return redirect()->route('admin.respondent-categories.index')
            ->with('success', 'Kategori responden berhasil diperbarui.');
    }

    public function update(Request $request, $role)
    {
        // Memeriksa apakah role yang diminta valid
        if (!in_array($role, ['mahasiswa', 'dosen', 'tendik', 'alumni', 'pengguna_lulusan', 'mitra'])) {
            return redirect()->route('admin.respondent-categories.index')
                ->with('error', 'Kategori responden tidak valid.');
        }

        // Validasi input dari form
        $request->validate([
            'questionnaire_ids' => 'nullable|array',
            'questionnaire_ids.*' => 'exists:questionnaires,id'
        ]);

        $questionnaireIds = $request->questionnaire_ids ?? [];

        // Menghapus semua izin yang ada untuk role ini
        QuestionnairePermission::where('role', $role)
            ->where('can_fill', true)
            ->delete();

        // Membuat izin baru untuk setiap kuesioner yang dipilih
        foreach ($questionnaireIds as $questionnaireId) {
            QuestionnairePermission::create([
                'questionnaire_id' => $questionnaireId,
                'role' => $role,
                'can_fill' => true,
                'can_view_results' => false
            ]);
        }

        return redirect()->route('admin.respondent-categories.index')
            ->with('success', 'Kategori responden ' . ucfirst($role) . ' berhasil diperbarui.');
    }
}
