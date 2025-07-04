<?php

namespace App\Http\Controllers;

use App\Models\AcademicPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicPeriodController extends Controller
{
    /**
     * Menampilkan semua periode akademik
     */
    public function index()
    {
        $academicPeriods = AcademicPeriod::orderBy('year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        return view('academic-periods.index', compact('academicPeriods'));
    }

    /**
     * Menyimpan periode akademik baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'semester' => 'required|string|in:Ganjil,Genap',
            'year' => 'required|string|max:9',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'sometimes|boolean'
        ]);

        DB::beginTransaction();

        try {
            // Jika periode baru diset aktif, nonaktifkan semua periode lain
            if (isset($validated['is_active']) && $validated['is_active']) {
                AcademicPeriod::where('is_active', true)->update(['is_active' => false]);
            }

            // Buat periode baru
            AcademicPeriod::create($validated);

            DB::commit();

            return redirect()->route('academic-periods.index')
                ->with('success', 'Periode akademik berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Memperbarui periode akademik
     */
    public function update(Request $request, AcademicPeriod $academicPeriod)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'semester' => 'required|string|in:Ganjil,Genap',
            'year' => 'required|string|max:9',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'sometimes|boolean'
        ]);

        // Default value untuk is_active jika tidak ada di request
        $validated['is_active'] = isset($validated['is_active']) ? true : false;

        DB::beginTransaction();

        try {
            // Jika periode diset aktif, nonaktifkan semua periode lain
            if ($validated['is_active']) {
                AcademicPeriod::where('id', '!=', $academicPeriod->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }

            // Update periode
            $academicPeriod->update($validated);

            DB::commit();

            return redirect()->route('academic-periods.index')
                ->with('success', 'Periode akademik berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Set periode akademik sebagai aktif
     */
    public function setActive(AcademicPeriod $academicPeriod)
    {
        DB::beginTransaction();

        try {
            // Nonaktifkan semua periode
            AcademicPeriod::where('is_active', true)->update(['is_active' => false]);

            // Set periode yang dipilih sebagai aktif
            $academicPeriod->update(['is_active' => true]);

            DB::commit();

            return redirect()->route('academic-periods.index')
                ->with('success', 'Periode akademik "' . $academicPeriod->name . '" sekarang aktif.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus periode akademik
     */
    public function destroy(AcademicPeriod $academicPeriod)
    {
        // Cek apakah ada kuesioner yang terkait
        if ($academicPeriod->questionnaires()->count() > 0) {
            return redirect()->route('academic-periods.index')
                ->with('error', 'Tidak dapat menghapus periode akademik yang memiliki kuesioner terkait.');
        }

        // Cek apakah periode sedang aktif
        if ($academicPeriod->is_active) {
            return redirect()->route('academic-periods.index')
                ->with('error', 'Tidak dapat menghapus periode akademik yang sedang aktif.');
        }

        $academicPeriod->delete();

        return redirect()->route('academic-periods.index')
            ->with('success', 'Periode akademik berhasil dihapus.');
    }
}
