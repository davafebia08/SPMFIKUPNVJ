<?php

namespace App\Http\Controllers;

use App\Models\AcademicPeriod;
use App\Models\Questionnaire;
use App\Models\Response;
use App\Models\User;
use App\Models\Suggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PublicQuestionnaireController extends Controller
{
    public function index()
    {
        return view('public.questionnaire.index');
    }

    public function showPenggunaLulusanForm(Request $request)
    {
        $activePeriod = AcademicPeriod::where('is_active', true)->first();

        if (!$activePeriod) {
            return redirect()->route('home')->with('error', 'Tidak ada periode akademik aktif saat ini.');
        }

        // Dapatkan kuesioner kepuasan pengguna lulusan yang aktif
        $questionnaire = Questionnaire::where('academic_period_id', $activePeriod->id)
            ->where('is_active', true)
            ->where('type', 'kepuasan_pengguna_lulusan')
            ->first();

        if (!$questionnaire) {
            return redirect()->route('home')->with('error', 'Kuesioner kepuasan pengguna lulusan tidak tersedia saat ini.');
        }

        // Jika sudah mengisi data responden
        if ($request->session()->has('responden_data')) {
            $responden = $request->session()->get('responden_data');
            $questions = $questionnaire->questions()->with('category')->orderBy('order')->get();

            return view('public.questionnaire.questionnaire_form', compact('questionnaire', 'responden', 'questions'));
        }

        return view('public.questionnaire.pengguna_lulusan_form', compact('questionnaire', 'activePeriod'));
    }

    public function showMitraForm(Request $request)
    {
        $activePeriod = AcademicPeriod::where('is_active', true)->first();

        if (!$activePeriod) {
            return redirect()->route('home')->with('error', 'Tidak ada periode akademik aktif saat ini.');
        }

        // Dapatkan kuesioner kepuasan mitra yang aktif
        $questionnaire = Questionnaire::where('academic_period_id', $activePeriod->id)
            ->where('is_active', true)
            ->where('type', 'kepuasan_mitra')
            ->first();

        if (!$questionnaire) {
            return redirect()->route('home')->with('error', 'Kuesioner kepuasan mitra tidak tersedia saat ini.');
        }

        // Jika sudah mengisi data responden
        if ($request->session()->has('responden_data')) {
            $responden = $request->session()->get('responden_data');
            $questions = $questionnaire->questions()->with('category')->orderBy('order')->get();

            return view('public.questionnaire.questionnaire_form', compact('questionnaire', 'responden', 'questions'));
        }

        // Jika belum mengisi data responden
        return view('public.questionnaire.mitra_form', compact('questionnaire', 'activePeriod'));
    }

    public function submitPenggunaLulusan(Request $request, Questionnaire $questionnaire)
    {
        // dd($request->all());
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'nama_instansi' => 'required|string|max:255',
            'nama_alumni' => 'required|string|max:255',
            'tahun_lulus_alumni' => 'required|string|max:4',
            'program_studi_alumni' => 'required|string|max:255',
            'ratings' => 'required|array',
            'ratings.*' => 'required|integer|min:1|max:4',
            'suggestion' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Buat user sementara untuk pengguna lulusan
            $temporaryUser = User::create([
                'name' => $request->name,
                'email' => 'temp_' . Str::random(10) . '@example.com',
                'username' => 'temp_' . Str::random(10),
                'password' => Hash::make(Str::random(16)),
                'role' => 'pengguna_lulusan',
                'jabatan' => $request->jabatan,
                'nama_instansi' => $request->nama_instansi,
                'meta_nama_alumni' => $request->nama_alumni,
                'meta_tahun_lulus_alumni' => $request->tahun_lulus_alumni,
                'meta_program_studi_alumni' => $request->program_studi_alumni,
                'is_active' => false,
            ]);

            // Simpan jawaban kuesioner
            foreach ($request->ratings as $questionId => $rating) {
                Response::create([
                    'questionnaire_id' => $questionnaire->id,
                    'question_id' => $questionId,
                    'user_id' => $temporaryUser->id,
                    'rating' => $rating,
                    'comment' => $request->comments[$questionId] ?? null,
                ]);
            }

            // Catat bahwa user ini telah mengisi kuesioner
            $questionnaire->users()->attach($temporaryUser->id, ['submitted_at' => now()]);

            // Simpan saran jika ada
            if (!empty($request->expectation)) {
                Suggestion::create([
                    'user_id' => $temporaryUser->id,
                    'questionnaire_id' => $questionnaire->id,
                    'content' => 'Harapan : ' . $request->expectation,
                    'status' => 'submitted'
                ]);
            }

            if (!empty($request->suggestion)) {
                Suggestion::create([
                    'user_id' => $temporaryUser->id,
                    'questionnaire_id' => $questionnaire->id,
                    'content' => 'Saran : ' . $request->suggestion,
                    'status' => 'submitted'
                ]);
            }

            DB::commit();

            // Hapus data responden dari session
            $request->session()->forget('responden_data');

            return redirect()->route('public.questionnaire.thanks');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    public function submitMitra(Request $request, Questionnaire $questionnaire)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'nama_instansi' => 'required|string|max:255',
            'jenis_mitra' => 'required|string|max:255',
            'jenis_kerjasama' => 'required|string|max:255',
            'lingkup_kerjasama' => 'required|string|max:255',
            'periode_kerjasama' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:15',
            'alamat' => 'required|string',
            'ratings' => 'required|array',
            'ratings.*' => 'required|integer|min:1|max:4',
            'suggestion' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Buat user sementara untuk mitra
            $temporaryUser = User::create([
                'name' => $request->name,
                'email' => 'temp_' . Str::random(10) . '@example.com',
                'username' => 'temp_' . Str::random(10),
                'password' => Hash::make(Str::random(16)),
                'role' => 'mitra',
                'jabatan' => $request->jabatan,
                'nama_instansi' => $request->nama_instansi,
                'no_telepon' => $request->no_telepon,
                'is_active' => false, // User ini tidak aktif (hanya untuk penyimpanan jawaban)
            ]);

            // Simpan metadata tambahan
            $temporaryUser->setAttribute('meta_jenis_mitra', $request->jenis_mitra);
            $temporaryUser->setAttribute('meta_jenis_kerjasama', $request->jenis_kerjasama);
            $temporaryUser->setAttribute('meta_lingkup_kerjasama', $request->lingkup_kerjasama);
            $temporaryUser->setAttribute('meta_periode_kerjasama', $request->periode_kerjasama);
            $temporaryUser->setAttribute('meta_alamat', $request->alamat);
            $temporaryUser->save();

            // Simpan jawaban kuesioner
            foreach ($request->ratings as $questionId => $rating) {
                Response::create([
                    'questionnaire_id' => $questionnaire->id,
                    'question_id' => $questionId,
                    'user_id' => $temporaryUser->id,
                    'rating' => $rating,
                    'comment' => $request->comments[$questionId] ?? null,
                ]);
            }

            // Catat bahwa user ini telah mengisi kuesioner
            $questionnaire->users()->attach($temporaryUser->id, ['submitted_at' => now()]);

            // Simpan saran jika ada
            if (!empty($request->mitra_suggestion)) {
                Suggestion::create([
                    'user_id' => $temporaryUser->id,
                    'questionnaire_id' => $questionnaire->id,
                    'content' => 'Saran dari Mitra: ' . $request->mitra_suggestion,
                    'status' => 'submitted'
                ]);
            }

            if (!empty($request->fik_suggestion)) {
                Suggestion::create([
                    'user_id' => $temporaryUser->id,
                    'questionnaire_id' => $questionnaire->id,
                    'content' => 'Saran Kemajuan FIK: ' . $request->fik_suggestion,
                    'status' => 'submitted'
                ]);
            }

            DB::commit();

            // Hapus data responden dari session
            $request->session()->forget('responden_data');

            return redirect()->route('public.questionnaire.thanks');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    public function thanks(Request $request)
    {
        // Hapus data responden dari session jika masih ada
        $request->session()->forget('responden_data');

        return view('public.questionnaire.thanks');
    }

    public function processPenggunaLulusanForm(Request $request, Questionnaire $questionnaire)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'nama_instansi' => 'required|string|max:255',
            'nama_alumni' => 'required|string|max:255',
            'tahun_lulus_alumni' => 'required|string|max:4',
            'program_studi_alumni' => 'required|string|max:255',
        ]);

        // Simpan data responden di session
        $request->session()->put('responden_data', $validated);

        // Redirect ke form kuesioner
        return redirect()->route('public.questionnaire.pengguna-lulusan');
    }

    public function processMitraForm(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'nama_instansi' => 'required|string|max:255',
            'jenis_mitra' => 'required|string|max:255',
            'jenis_kerjasama' => 'required|string|max:255',
            'lingkup_kerjasama' => 'required|string|max:255',
            'periode_kerjasama' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:15',
            'alamat' => 'required|string',
        ]);

        // Simpan data responden di session
        $request->session()->put('responden_data', $validated);

        // Redirect ke form kuesioner
        return redirect()->route('public.questionnaire.mitra');
    }
}
