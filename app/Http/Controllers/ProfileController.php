<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Tentukan validasi berdasarkan role
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'no_telepon' => 'nullable|string|max:15',
        ];

        // Tambahkan validasi khusus berdasarkan role
        if ($user->role === 'alumni') {
            $rules['tahun_lulus'] = 'nullable|string|max:4';
            $rules['program_studi'] = 'required|string|max:255';
            $rules['nik'] = [
                'nullable',
                'string',
                'max:16',
                Rule::unique('users')->ignore($user->id),
            ];
            $rules['npwp'] = 'nullable|string|max:20';
            $rules['domisili'] = 'nullable|string|max:255';
        } elseif ($user->role === 'mahasiswa') {
            // Mahasiswa seharusnya tidak perlu mengubah program studi,
            // karena biasanya program studi sudah tetap dari SIAKAD
            // Jadi kita tidak validate field ini untuk mahasiswa
        } elseif (in_array($user->role, ['pengguna_lulusan', 'mitra'])) {
            $rules['nama_instansi'] = 'nullable|string|max:255';
            $rules['jabatan'] = 'nullable|string|max:255';
        }

        $validatedData = $request->validate($rules);

        // Pastikan program_studi tetap ada jika user adalah mahasiswa
        if ($user->role === 'mahasiswa' && !isset($validatedData['program_studi'])) {
            $validatedData['program_studi'] = $user->program_studi;
        }

        $user->update($validatedData);

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();

        // Periksa dan buat direktori jika belum ada
        $storagePath = storage_path('app/public/profile-photos');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        // Hapus foto lama jika ada
        if ($user->profile_photo && Storage::disk('public')->exists('profile-photos/' . $user->profile_photo)) {
            Storage::disk('public')->delete('profile-photos/' . $user->profile_photo);
        }

        // Simpan foto baru
        if ($request->hasFile('profile_photo')) {
            $profilePhoto = $request->file('profile_photo');
            $filename = time() . '_' . Auth::id() . '.' . $profilePhoto->getClientOriginalExtension();
            $path = $profilePhoto->storeAs('profile-photos', $filename, 'public');

            if ($path) {
                $user->profile_photo = $filename;
                $user->save();

                return redirect()->route('profile.edit')->with('success', 'Foto profil berhasil diperbarui.');
            } else {

                return redirect()->route('profile.edit')->with('error', 'Gagal menyimpan foto profil. Silakan coba lagi.');
            }
        }

        return redirect()->route('profile.edit')->with('error', 'Terjadi kesalahan saat mengunggah foto.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Password berhasil diubah.');
    }
}
