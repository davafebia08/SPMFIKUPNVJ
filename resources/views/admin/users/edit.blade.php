@extends('layouts.admin')

@section('title', 'Edit User - Admin SPM FIK UPNVJ')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Edit User</h1>
                <p class="mb-0 text-muted">Edit informasi pengguna sistem SPM</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <!-- Edit User Form Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Edit User</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Profile Photo -->
                        <div class="col-md-3 mb-4 text-center">
                            <div class="d-flex flex-column align-items-center">
                                @if ($user->profile_photo)
                                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="img-thumbnail rounded-circle mb-3"
                                        style="width: 150px; height: 150px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mb-3"
                                        style="width: 150px; height: 150px">
                                        <i class="fas fa-user fa-4x text-white"></i>
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <label for="profile_photo" class="form-label">Ganti Foto Profil</label>
                                    <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" id="profile_photo"
                                        name="profile_photo">
                                    @error('profile_photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Format: JPG, PNG, JPEG. Maks: 2MB</div>
                                </div>
                            </div>
                        </div>

                        <!-- User Information -->
                        <div class="col-md-9">
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                        value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                        value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label">Kategori User <span class="text-danger">*</span></label>
                                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                        @foreach ($roles as $value => $label)
                                            <option value="{{ $value }}" {{ old('role', $user->role) === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                            {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Aktif</label>
                                    </div>
                                </div>

                                <!-- Additional Information (conditionally shown based on role) -->
                                <div class="col-12">
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <h6 class="m-0">Informasi Tambahan</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row role-fields" id="fields-mahasiswa">
                                                <div class="col-md-6 mb-3">
                                                    <label for="nim" class="form-label">NIM</label>
                                                    <input type="text" class="form-control @error('nim') is-invalid @enderror" id="nim"
                                                        name="nim" value="{{ old('nim', $user->nim) }}">
                                                    @error('nim')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="program_studi" class="form-label">Program Studi</label>
                                                    <select class="form-select @error('program_studi') is-invalid @enderror" id="program_studi"
                                                        name="program_studi">
                                                        <option value="" disabled>Pilih Program Studi</option>
                                                        <option value="S1 Informatika"
                                                            {{ old('program_studi', $user->program_studi) == 'S1 Informatika' ? 'selected' : '' }}>S1
                                                            Informatika</option>
                                                        <option value="S1 Sains Data"
                                                            {{ old('program_studi', $user->program_studi) == 'S1 Sains Data' ? 'selected' : '' }}>S1
                                                            Sains Data</option>
                                                        <option value="S1 Sistem Informasi"
                                                            {{ old('program_studi', $user->program_studi) == 'S1 Sistem Informasi' ? 'selected' : '' }}>
                                                            S1 Sistem Informasi</option>
                                                        <option value="D3 Sistem Informasi"
                                                            {{ old('program_studi', $user->program_studi) == 'D3 Sistem Informasi' ? 'selected' : '' }}>
                                                            D3 Sistem Informasi</option>
                                                    </select>
                                                    @error('program_studi')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row role-fields" id="fields-dosen">
                                                <div class="col-md-6 mb-3">
                                                    <label for="nip" class="form-label">NIDN</label>
                                                    <input type="text" class="form-control @error('nip') is-invalid @enderror" id="nip"
                                                        name="nip" value="{{ old('nip', $user->nip) }}">
                                                    @error('nip')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="dosen_program_studi" class="form-label">Program Studi</label>
                                                    <select class="form-select @error('program_studi') is-invalid @enderror" id="dosen_program_studi"
                                                        name="program_studi">
                                                        <option value="" disabled>Pilih Program Studi</option>
                                                        <option value="S1 Informatika"
                                                            {{ old('program_studi', $user->program_studi) == 'S1 Informatika' ? 'selected' : '' }}>S1
                                                            Informatika</option>
                                                        <option value="S1 Sains Data"
                                                            {{ old('program_studi', $user->program_studi) == 'S1 Sains Data' ? 'selected' : '' }}>S1
                                                            Sains Data</option>
                                                        <option value="S1 Sistem Informasi"
                                                            {{ old('program_studi', $user->program_studi) == 'S1 Sistem Informasi' ? 'selected' : '' }}>
                                                            S1 Sistem Informasi</option>
                                                        <option value="D3 Sistem Informasi"
                                                            {{ old('program_studi', $user->program_studi) == 'D3 Sistem Informasi' ? 'selected' : '' }}>
                                                            D3 Sistem Informasi</option>
                                                    </select>
                                                    @error('program_studi')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row role-fields" id="fields-tendik">
                                                <div class="col-md-6 mb-3">
                                                    <label for="nik" class="form-label">NIK</label>
                                                    <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik"
                                                        name="nik" value="{{ old('nik', $user->nik) }}">
                                                    @error('nik')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row role-fields" id="fields-alumni">
                                                <div class="col-md-6 mb-3">
                                                    <label for="alumni_program_studi" class="form-label">Program Studi</label>
                                                    <select class="form-select @error('program_studi') is-invalid @enderror" id="alumni_program_studi"
                                                        name="program_studi">
                                                        <option value="" disabled>Pilih Program Studi</option>
                                                        <option value="S1 Informatika"
                                                            {{ old('program_studi', $user->program_studi) == 'S1 Informatika' ? 'selected' : '' }}>S1
                                                            Informatika</option>
                                                        <option value="S1 Sains Data"
                                                            {{ old('program_studi', $user->program_studi) == 'S1 Sains Data' ? 'selected' : '' }}>S1
                                                            Sains Data</option>
                                                        <option value="S1 Sistem Informasi"
                                                            {{ old('program_studi', $user->program_studi) == 'S1 Sistem Informasi' ? 'selected' : '' }}>
                                                            S1 Sistem Informasi</option>
                                                        <option value="D3 Sistem Informasi"
                                                            {{ old('program_studi', $user->program_studi) == 'D3 Sistem Informasi' ? 'selected' : '' }}>
                                                            D3 Sistem Informasi</option>
                                                    </select>
                                                    @error('program_studi')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="tahun_lulus" class="form-label">Tahun Lulus</label>
                                                    <input type="text" class="form-control @error('tahun_lulus') is-invalid @enderror"
                                                        id="tahun_lulus" name="tahun_lulus" value="{{ old('tahun_lulus', $user->tahun_lulus) }}">
                                                    @error('tahun_lulus')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row role-fields" id="fields-instansi">
                                                <div class="col-md-6 mb-3">
                                                    <label for="nama_instansi" class="form-label">Nama Instansi</label>
                                                    <input type="text" class="form-control @error('nama_instansi') is-invalid @enderror"
                                                        id="nama_instansi" name="nama_instansi"
                                                        value="{{ old('nama_instansi', $user->nama_instansi) }}">
                                                    @error('nama_instansi')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="jabatan" class="form-label">Jabatan</label>
                                                    <input type="text" class="form-control @error('jabatan') is-invalid @enderror" id="jabatan"
                                                        name="jabatan" value="{{ old('jabatan', $user->jabatan) }}">
                                                    @error('jabatan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="no_telepon" class="form-label">Nomor Telepon</label>
                                                    <input type="text" class="form-control @error('no_telepon') is-invalid @enderror"
                                                        id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}">
                                                    @error('no_telepon')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Password Section -->
                                <div class="col-12">
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <h6 class="m-0">Ubah Password (kosongkan jika tidak ingin mengubah)</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="password" class="form-label">Password Baru</label>
                                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                                                        name="password">
                                                    @error('password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                                    <input type="password" class="form-control" id="password_confirmation"
                                                        name="password_confirmation">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to show/hide fields based on selected role
            function toggleRoleFields() {
                const role = document.getElementById('role').value;

                // Hide all role-specific fields first
                document.querySelectorAll('.role-fields').forEach(el => {
                    el.style.display = 'none';
                });

                // Show fields based on selected role
                if (role === 'mahasiswa') {
                    document.getElementById('fields-mahasiswa').style.display = 'flex';
                } else if (role === 'dosen') {
                    document.getElementById('fields-dosen').style.display = 'flex';
                } else if (role === 'tendik') {
                    document.getElementById('fields-tendik').style.display = 'flex';
                } else if (role === 'alumni') {
                    document.getElementById('fields-alumni').style.display = 'flex';
                } else if (role === 'pengguna_lulusan' || role === 'mitra') {
                    document.getElementById('fields-instansi').style.display = 'flex';
                }
            }

            // Initial toggle based on the selected role
            toggleRoleFields();

            // Listen for changes to the role dropdown
            document.getElementById('role').addEventListener('change', toggleRoleFields);
        });
    </script>
@endsection
