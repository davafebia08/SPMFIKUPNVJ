@extends('layouts.app')

@section('title', 'Edit Profil - SPM FIK UPNVJ')

@section('content')
    <div class="container py-4">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 fw-bold text-orange">
                    <i class="fas fa-user-edit me-2"></i> Profil Saya
                </h1>
                <p class="text-muted mb-0">
                    Kelola informasi akun dan data pribadi Anda
                </p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Foto Profil Card -->
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100">
                    <div class="card-header bg-orange text-white py-3">
                        <h3 class="h5 mb-0 fw-bold">
                            <i class="fas fa-image me-2"></i> Foto Profil
                        </h3>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="mb-4">
                            @if (Auth::user()->profile_photo)
                                <img src="{{ Auth::user()->getProfilePhotoUrlAttribute() }}" alt="Foto Profil" class="rounded-circle img-thumbnail"
                                    style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto"
                                    style="width: 150px; height: 150px;">
                                    <i class="fas fa-user-circle fa-5x text-muted"></i>
                                </div>
                            @endif
                        </div>

                        <form action="{{ route('profile.update-photo') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="profile_photo" class="form-label">Unggah Foto Baru</label>
                                <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" id="profile_photo"
                                    name="profile_photo" accept="image/*">
                                @error('profile_photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Format: JPG, PNG. Maks 2MB</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-upload me-2"></i> Unggah Foto
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Informasi Akun Card -->
            <div class="col-md-8 mb-4">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-header bg-orange text-white py-3">
                        <h3 class="h5 mb-0 fw-bold">
                            <i class="fas fa-user-cog me-2"></i> Informasi Akun
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                        value="{{ old('name', Auth::user()->name) }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="role" class="form-label">Peran</label>
                                    <select class="form-select" id="role" disabled>
                                        <option value="mahasiswa" {{ Auth::user()->role == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                        <option value="dosen" {{ Auth::user()->role == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                        <option value="tendik" {{ Auth::user()->role == 'tendik' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                                        <option value="alumni" {{ Auth::user()->role == 'alumni' ? 'selected' : '' }}>Alumni</option>
                                        <option value="pengguna_lulusan" {{ Auth::user()->role == 'pengguna_lulusan' ? 'selected' : '' }}>Pengguna
                                            Lulusan</option>
                                        <option value="mitra" {{ Auth::user()->role == 'mitra' ? 'selected' : '' }}>Mitra</option>
                                        <option value="admin" {{ Auth::user()->role == 'admin' ? 'selected' : '' }}>Administrator</option>
                                        <option value="pimpinan" {{ Auth::user()->role == 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
                                    </select>
                                    <!-- Hidden input untuk menyimpan role asli -->
                                    <input type="hidden" name="role" value="{{ Auth::user()->role }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                        value="{{ old('email', Auth::user()->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username"
                                        value="{{ old('username', Auth::user()->username) }}">
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            @if (Auth::user()->role === 'mahasiswa')
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="nim" class="form-label">NIM</label>
                                        <input type="text" class="form-control" id="nim" value="{{ Auth::user()->nim }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="program_studi_display" class="form-label">Program Studi</label>
                                        <select class="form-select" id="program_studi_display" disabled>
                                            <option value="" selected disabled>Pilih Program Studi</option>
                                            <option value="S1 Informatika" {{ old('program_studi_alumni') == 'S1 Informatika' ? 'selected' : '' }}>S1
                                                Informatika</option>
                                            <option value="S1 Sains Data" {{ old('program_studi_alumni') == 'S1 Sains Data' ? 'selected' : '' }}>S1
                                                Sains Data</option>
                                            <option value="S1 Sistem Informasi"
                                                {{ old('program_studi_alumni') == 'S1 Sistem Informasi' ? 'selected' : '' }}>S1 Sistem Informasi</option>
                                            <option value="D3 Sistem Informasi"
                                                {{ old('program_studi_alumni') == 'D3 Sistem Informasi' ? 'selected' : '' }}>D3 Sistem Informasi</option>
                                        </select>
                                        <!-- Hidden input untuk memastikan nilai tetap dikirim meskipun select disabled -->
                                        <input type="hidden" name="program_studi" value="{{ Auth::user()->program_studi }}">
                                    </div>
                                </div>
                            @elseif(Auth::user()->role === 'dosen')
                                <div class="mb-4">
                                    <label for="nip" class="form-label">NIP</label>
                                    <input type="text" class="form-control" id="nip" value="{{ Auth::user()->nip }}" readonly>
                                </div>
                            @elseif(Auth::user()->role === 'tendik')
                                <div class="mb-4">
                                    <label for="nik" class="form-label">NIK</label>
                                    <input type="text" class="form-control" id="nik" value="{{ Auth::user()->nik }}" readonly>
                                </div>
                            @elseif(Auth::user()->role === 'alumni')
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="tahun_lulus" class="form-label">Tahun Lulus</label>
                                        <input type="text" class="form-control @error('tahun_lulus') is-invalid @enderror" id="tahun_lulus"
                                            name="tahun_lulus" value="{{ old('tahun_lulus', Auth::user()->tahun_lulus) }}">
                                        @error('tahun_lulus')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="program_studi" class="form-label">Program Studi</label>
                                        <select class="form-select @error('program_studi') is-invalid @enderror" id="program_studi"
                                            name="program_studi">
                                            <option value="">Pilih Program Studi</option>
                                            <option value="S1 Informatika"
                                                {{ old('program_studi', Auth::user()->program_studi) == 'S1 Informatika' ? 'selected' : '' }}>S1
                                                Informatika
                                            </option>
                                            <option value="S1 Sistem Informasi"
                                                {{ old('program_studi', Auth::user()->program_studi) == 'S1 Sistem Informasi' ? 'selected' : '' }}>S1
                                                Sistem Informasi
                                            </option>
                                            <option value="S1 Sains Data"
                                                {{ old('program_studi', Auth::user()->program_studi) == 'S1 Sains Data' ? 'selected' : '' }}>S1 Sains
                                                Data
                                            </option>
                                            <option value="D3 Sistem Informasi"
                                                {{ old('program_studi', Auth::user()->program_studi) == 'D3 Sistem Informasi' ? 'selected' : '' }}>D3
                                                Sistem Informasi
                                            </option>
                                        </select>
                                        @error('program_studi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="nik" class="form-label">NIK</label>
                                        <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik"
                                            value="{{ old('nik', Auth::user()->nik) }}">
                                        @error('nik')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="npwp" class="form-label">NPWP</label>
                                        <input type="text" class="form-control @error('npwp') is-invalid @enderror" id="npwp" name="npwp"
                                            value="{{ old('npwp', Auth::user()->npwp) }}">
                                        @error('npwp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="domisili" class="form-label">Domisili</label>
                                    <input type="text" class="form-control @error('domisili') is-invalid @enderror" id="domisili" name="domisili"
                                        value="{{ old('domisili', Auth::user()->domisili) }}">
                                    @error('domisili')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @elseif(Auth::user()->role === 'pengguna_lulusan' || Auth::user()->role === 'mitra')
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="nama_instansi" class="form-label">Nama Instansi</label>
                                        <input type="text" class="form-control @error('nama_instansi') is-invalid @enderror" id="nama_instansi"
                                            name="nama_instansi" value="{{ old('nama_instansi', Auth::user()->nama_instansi) }}">
                                        @error('nama_instansi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="jabatan" class="form-label">Jabatan</label>
                                        <input type="text" class="form-control @error('jabatan') is-invalid @enderror" id="jabatan"
                                            name="jabatan" value="{{ old('jabatan', Auth::user()->jabatan) }}">
                                        @error('jabatan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            <div class="mb-4">
                                <label for="no_telepon" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control @error('no_telepon') is-invalid @enderror" id="no_telepon"
                                    name="no_telepon" value="{{ old('no_telepon', Auth::user()->no_telepon) }}">
                                @error('no_telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Ubah Password Card -->
            <div class="col-md-12">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-header bg-orange text-white py-3">
                        <h3 class="h5 mb-0 fw-bold">
                            <i class="fas fa-lock me-2"></i> Ubah Password
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('profile.update-password') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="current_password" class="form-label">Password Saat Ini</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password"
                                        name="current_password">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="password" class="form-label">Password Baru</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                                        name="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i> Password minimal 8 karakter
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key me-2"></i> Ubah Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Preview foto profil saat file dipilih
            const photoInput = document.getElementById('profile_photo');
            photoInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.querySelector('.img-thumbnail');
                        if (img) {
                            img.src = e.target.result;
                        } else {
                            const placeholderDiv = document.querySelector('.rounded-circle.bg-light');
                            if (placeholderDiv) {
                                placeholderDiv.innerHTML =
                                    `<img src="${e.target.result}" alt="Foto Profil" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">`;
                            }
                        }
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    </script>
@endsection
