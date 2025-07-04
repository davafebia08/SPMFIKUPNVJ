<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UpnvjApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SSOController extends Controller
{
    protected $upnvjApiService;

    public function __construct(UpnvjApiService $upnvjApiService)
    {
        $this->upnvjApiService = $upnvjApiService;
    }

    public function testApi(Request $request)
    {
        $identifier = $request->input('identifier');
        $password = $request->input('password');

        $response = $this->upnvjApiService->authenticate($identifier, $password);

        return response()->json($response);
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Coba login lokal dulu dengan email/username
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        // Coba login dengan username
        $user = User::where('username', $credentials['email'])->first();
        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        // Coba login dengan nik, nim, atau nip
        $user = User::where(function ($query) use ($credentials) {
            $query->where('nik', $credentials['email'])
                ->orWhere('nim', $credentials['email'])
                ->orWhere('nip', $credentials['email']);
        })->first();
        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        // Jika gagal, coba sebagai login dengan API UPNVJ
        try {
            $apiResponse = $this->upnvjApiService->authenticate($credentials['email'], $credentials['password']);

            // PERBAIKAN: Periksa respons API dengan lebih teliti
            if (isset($apiResponse['success']) && $apiResponse['success']) {
                // Ekstrak data dengan benar
                $apiData = $apiResponse['data'] ?? [];
                $userInfo = $apiData['data'] ?? $apiData;

                // PERBAIKAN: Pastikan data yang diperlukan ada dan valid
                // Periksa data pengguna yang dikembalikan
                if (!isset($userInfo['nama']) || empty(trim($userInfo['nama'])) || $userInfo['nama'] == 'Unknown') {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'email' => 'Kredensial tidak valid atau data pengguna tidak lengkap.'
                        ]);
                }

                // Tentukan role berdasarkan pola NIM/NIP/NIK
                $identifier = $credentials['email'];
                $role = $this->determineRole($identifier);

                // Cek apakah user sudah terdaftar
                $user = User::where(function ($query) use ($identifier) {
                    $query->where('nim', $identifier)
                        ->orWhere('nip', $identifier)
                        ->orWhere('nik', $identifier)
                        ->orWhere('username', $identifier);
                })->first();

                if (!$user) {
                    // Jika belum ada, buat user baru
                    $userData = [
                        'name' => $userInfo['nama'] ?? 'Unknown',
                        'email' => $userInfo['email'] ?? $identifier . '@upnvj.ac.id',
                        'username' => $identifier,
                        'password' => Hash::make($credentials['password']),
                        'role' => $role,
                        'no_telepon' => $userInfo['hp'] ?? null,
                    ];

                    // Tambahkan field sesuai role
                    if ($role === 'mahasiswa') {
                        $userData['nim'] = $identifier;
                        $userData['program_studi'] = $userInfo['nama_program_studi'] ?? null;
                    } elseif ($role === 'dosen') {
                        $userData['nip'] = $identifier;
                        $userData['program_studi'] = $userInfo['nama_program_studi'] ?? null;
                    } elseif ($role === 'tendik') {
                        $userData['nik'] = $identifier;
                    }

                    $user = User::create($userData);
                }

                // Login user
                Auth::login($user);
                $request->session()->regenerate();
                return redirect()->intended('dashboard');
            }

            // PERBAIKAN: Tampilkan pesan error yang jelas dari API
            return back()
                ->withInput()
                ->withErrors([
                    'email' => $apiResponse['message'] ?? 'Kredensial yang diberikan tidak valid.'
                ]);
        } catch (\Exception $e) {
            // Log error
            Log::error('SSO Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['system_error' => 'Terjadi kesalahan sistem. Silakan coba lagi.']);
        }
    }

    private function determineRole($identifier)
    {
        // Asumsi pola NIM: 8-10 digit dimulai dengan 2
        if (preg_match('/^2\d{7,9}$/', $identifier)) {
            return 'mahasiswa';
        }

        // Asumsi pola NIP: 18 digit
        if (preg_match('/^\d{18}$/', $identifier)) {
            return 'dosen';
        }

        // Asumsi pola NIK: 16 digit
        if (preg_match('/^\d{16}$/', $identifier)) {
            return 'tendik';
        }

        // Default jika tidak cocok dengan pola apapun
        return 'user';
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
