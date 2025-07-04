<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'tahun_lulus' => ['required', 'string', 'max:4'],
            'tahun_angkatan' => ['required', 'string', 'max:4'],
            'program_studi' => ['required', 'string'],
            'nik' => ['required', 'string', 'max:16', 'unique:users'],
            'npwp' => ['nullable', 'string', 'max:20'],
            'domisili' => ['required', 'string', 'max:255'],
            'no_telepon' => ['nullable', 'string', 'max:15'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'alumni',
            'tahun_lulus' => $request->tahun_lulus,
            'tahun_angkatan' => $request->tahun_angkatan,
            'program_studi' => $request->program_studi,
            'nik' => $request->nik,
            'npwp' => $request->npwp,
            'domisili' => $request->domisili,
            'no_telepon' => $request->no_telepon,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil. Selamat datang di Sistem Pelayanan Minimal FIK UPNVJ!');
    }
}
