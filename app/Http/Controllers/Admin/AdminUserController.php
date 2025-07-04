<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Filter berdasarkan role jika ada
        if ($request->has('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        // Filter berdasarkan search query jika ada
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('nim', 'like', "%{$searchTerm}%")
                    ->orWhere('nip', 'like', "%{$searchTerm}%")
                    ->orWhere('program_studi', 'like', "%{$searchTerm}%");
            });
        }

        // Urutkan berdasarkan role, lalu nama
        $users = $query->orderBy('role')->orderBy('name')->paginate(10);

        return view('admin.users.index', [
            'users' => $users,
            'roleFilter' => $request->role ?? 'all',
            'searchQuery' => $request->search ?? '',
            'roles' => [
                'all' => 'Semua Pengguna',
                'mahasiswa' => 'Mahasiswa',
                'dosen' => 'Dosen',
                'tendik' => 'Tenaga Kependidikan',
                'alumni' => 'Alumni',
                'pengguna_lulusan' => 'Pengguna Lulusan',
                'mitra' => 'Mitra',
                'admin' => 'Administrator',
                'pimpinan' => 'Pimpinan'
            ]
        ]);
    }

    public function create()
    {
        return view('admin.users.create', [
            'roles' => [
                'mahasiswa' => 'Mahasiswa',
                'dosen' => 'Dosen',
                'tendik' => 'Tenaga Kependidikan',
                'alumni' => 'Alumni',
                'pengguna_lulusan' => 'Pengguna Lulusan',
                'mitra' => 'Mitra',
                'admin' => 'Administrator',
                'pimpinan' => 'Pimpinan'
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'role' => 'required|in:mahasiswa,dosen,tendik,alumni,pengguna_lulusan,mitra,admin,pimpinan',
            'nim' => 'nullable|string|max:20|unique:users,nim',
            'nip' => 'nullable|string|max:20|unique:users,nip',
            'nik' => 'nullable|string|max:20|unique:users,nik',
            'program_studi' => 'nullable|string|max:100',
            'tahun_lulus' => 'nullable|string|max:10',
            'nama_instansi' => 'nullable|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'no_telepon' => 'nullable|string|max:15',
            'password' => 'required|string|min:8|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Hash password
        $validatedData['password'] = Hash::make($validatedData['password']);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $profilePhoto = $request->file('profile_photo');
            $fileName = time() . '.' . $profilePhoto->getClientOriginalExtension();
            $profilePhoto->storeAs('profile-photos', $fileName, 'public');

            $validatedData['profile_photo'] = $fileName;
        }

        // Set is_active default value
        $validatedData['is_active'] = $request->has('is_active') ? true : false;

        $user = User::create($validatedData);

        return redirect()->route('admin.users.index')->with('success', 'User baru berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => [
                'mahasiswa' => 'Mahasiswa',
                'dosen' => 'Dosen',
                'tendik' => 'Tenaga Kependidikan',
                'alumni' => 'Alumni',
                'pengguna_lulusan' => 'Pengguna Lulusan',
                'mitra' => 'Mitra',
                'admin' => 'Administrator',
                'pimpinan' => 'Pimpinan'
            ]
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => 'required|in:mahasiswa,dosen,tendik,alumni,pengguna_lulusan,mitra,admin,pimpinan',
            'nim' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users')->ignore($user->id),
            ],
            'nip' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users')->ignore($user->id),
            ],
            'nik' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users')->ignore($user->id),
            ],
            'program_studi' => 'nullable|string|max:100',
            'tahun_lulus' => 'nullable|string|max:10',
            'nama_instansi' => 'nullable|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'no_telepon' => 'nullable|string|max:15',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Handle password update
        if (isset($validatedData['password']) && $validatedData['password']) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old image if exists
            if ($user->profile_photo) {
                Storage::disk('public')->delete('profile-photos/' . $user->profile_photo);
            }

            $profilePhoto = $request->file('profile_photo');
            $fileName = time() . '.' . $profilePhoto->getClientOriginalExtension();
            $profilePhoto->storeAs('profile-photos', $fileName, 'public');

            $validatedData['profile_photo'] = $fileName;
        }

        // Set is_active value
        $validatedData['is_active'] = $request->has('is_active') ? true : false;

        $user->update($validatedData);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting self
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Check if the user has responses or participated in questionnaires
        if ($user->responses()->count() > 0 || $user->questionnaires()->count() > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Pengguna ini memiliki data respons kuesioner. Hapus data tersebut terlebih dahulu.');
        }

        // Remove profile photo if exists
        if ($user->profile_photo) {
            Storage::disk('public')->delete('profile-photos/' . $user->profile_photo);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
