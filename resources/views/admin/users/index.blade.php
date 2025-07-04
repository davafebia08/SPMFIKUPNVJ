@extends('layouts.admin')

@section('title', 'Kelola User - Admin SPM FIK UPNVJ')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Kelola User</h1>
                <p class="mb-0 text-muted">Mengelola data user sistem SPM</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah User Baru
            </a>
        </div>

        <!-- Filter Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filter User</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="role" class="form-label">Kategori User</label>
                            <select class="form-select" id="role" name="role">
                                @foreach ($roles as $value => $label)
                                    <option value="{{ $value }}" {{ $roleFilter === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="search" class="form-label">Cari</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ $searchQuery }}"
                                placeholder="Cari berdasarkan nama, email, NIM, NIDN, atau program studi">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Cari
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar User</h6>
                <span class="badge bg-primary">{{ $users->total() }} Pengguna</span>
            </div>
            <div class="card-body">
                @if ($users->isEmpty())
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-1"></i> Tidak ada data pengguna yang ditemukan
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead class="table-light ">
                                <tr>
                                    <th style="width: 50px">No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Kategori</th>
                                    <th>Informasi Tambahan</th>
                                    <th>Status</th>
                                    <th style="width: 120px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $index => $user)
                                    @php
                                        $roleLabels = [
                                            'mahasiswa' => 'Mahasiswa',
                                            'dosen' => 'Dosen',
                                            'tendik' => 'Tenaga Kependidikan',
                                            'alumni' => 'Alumni',
                                            'pengguna_lulusan' => 'Pengguna Lulusan',
                                            'mitra' => 'Mitra',
                                            'admin' => 'Administrator',
                                            'pimpinan' => 'Pimpinan',
                                        ];
                                    @endphp
                                    <tr>
                                        <td>{{ $users->firstItem() + $index }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($user->profile_photo)
                                                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="rounded-circle me-2"
                                                        width="32" height="32">
                                                @else
                                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                        style="width: 32px; height: 32px">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                @endif
                                                {{ $user->name }}
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @php
                                                $badgeClass = 'bg-secondary';

                                                if ($user->role === 'admin') {
                                                    $badgeClass = 'bg-danger';
                                                } elseif ($user->role === 'pimpinan') {
                                                    $badgeClass = 'bg-primary';
                                                } elseif ($user->role === 'mahasiswa') {
                                                    $badgeClass = 'bg-info';
                                                } elseif ($user->role === 'dosen') {
                                                    $badgeClass = 'bg-success';
                                                } elseif ($user->role === 'alumni') {
                                                    $badgeClass = 'bg-warning';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($user->role === 'mahasiswa')
                                                NIM: {{ $user->nim ?? '-' }}<br>
                                                Program Studi: {{ $user->program_studi ?? '-' }}
                                            @elseif ($user->role === 'dosen')
                                                NIDN: {{ $user->nip ?? '-' }}
                                            @elseif ($user->role === 'tendik')
                                                NIK: {{ $user->nik ?? '-' }}
                                            @elseif ($user->role === 'alumni')
                                                Program Studi: {{ $user->program_studi ?? '-' }}<br>
                                                Tahun Lulus: {{ $user->tahun_lulus ?? '-' }}
                                            @elseif ($user->role === 'pengguna_lulusan' || $user->role === 'mitra')
                                                Instansi: {{ $user->nama_instansi ?? '-' }}<br>
                                                Jabatan: {{ $user->jabatan ?? '-' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($user->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Non-Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary" title="Edit User">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if (auth()->id() !== $user->id)
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger btn-delete" title="Hapus User">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $users->appends(request()->query())->links('vendor.pagination.default') }}
                    </div>

                @endif
            </div>
        </div>
    </div>
@endsection
