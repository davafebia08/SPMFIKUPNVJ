@extends('layouts.app')

@section('title', 'Registrasi Alumni - SPM FIK UPNVJ')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-xl">
                    <div class="card-header bg-orange text-white py-3 text-center">
                        <h4 class="mb-0 fw-bold">Registrasi Akun Alumni</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <img src="{{ asset('images/logoupn.png') }}" alt="Logo FIK UPNVJ" height="80">
                            <p class="mt-3 text-muted">Silakan lengkapi data diri Anda sebagai alumni FIK UPNVJ</p>
                        </div>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                    value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Alamat Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                    value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username"
                                    value="{{ old('username') }}" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nik" class="form-label">NIK (Nomor Induk Kependudukan)</label>
                                <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik"
                                    value="{{ old('nik') }}" required maxlength="16" placeholder="Contoh: 3201012304990001">
                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="npwp" class="form-label">NPWP (Opsional)</label>
                                <input type="text" class="form-control @error('npwp') is-invalid @enderror" id="npwp" name="npwp"
                                    value="{{ old('npwp') }}" placeholder="Contoh: 99.999.999.9-999.999">
                                @error('npwp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="domisili" class="form-label">Domisili Saat Ini</label>
                                <select class="form-select @error('domisili') is-invalid @enderror" id="domisili" name="domisili" required>
                                    <option value="" selected disabled>Pilih Provinsi</option>
                                    <option value="Aceh" {{ old('domisili') == 'Aceh' ? 'selected' : '' }}>Aceh</option>
                                    <option value="Sumatera Utara" {{ old('domisili') == 'Sumatera Utara' ? 'selected' : '' }}>Sumatera Utara</option>
                                    <option value="Sumatera Barat" {{ old('domisili') == 'Sumatera Barat' ? 'selected' : '' }}>Sumatera Barat</option>
                                    <option value="Riau" {{ old('domisili') == 'Riau' ? 'selected' : '' }}>Riau</option>
                                    <option value="Kepulauan Riau" {{ old('domisili') == 'Kepulauan Riau' ? 'selected' : '' }}>Kepulauan Riau</option>
                                    <option value="Jambi" {{ old('domisili') == 'Jambi' ? 'selected' : '' }}>Jambi</option>
                                    <option value="Sumatera Selatan" {{ old('domisili') == 'Sumatera Selatan' ? 'selected' : '' }}>Sumatera Selatan
                                    </option>
                                    <option value="Kepulauan Bangka Belitung" {{ old('domisili') == 'Kepulauan Bangka Belitung' ? 'selected' : '' }}>
                                        Kepulauan Bangka Belitung</option>
                                    <option value="Bengkulu" {{ old('domisili') == 'Bengkulu' ? 'selected' : '' }}>Bengkulu</option>
                                    <option value="Lampung" {{ old('domisili') == 'Lampung' ? 'selected' : '' }}>Lampung</option>
                                    <option value="DKI Jakarta" {{ old('domisili') == 'DKI Jakarta' ? 'selected' : '' }}>DKI Jakarta</option>
                                    <option value="Jawa Barat" {{ old('domisili') == 'Jawa Barat' ? 'selected' : '' }}>Jawa Barat</option>
                                    <option value="Banten" {{ old('domisili') == 'Banten' ? 'selected' : '' }}>Banten</option>
                                    <option value="Jawa Tengah" {{ old('domisili') == 'Jawa Tengah' ? 'selected' : '' }}>Jawa Tengah</option>
                                    <option value="DI Yogyakarta" {{ old('domisili') == 'DI Yogyakarta' ? 'selected' : '' }}>DI Yogyakarta</option>
                                    <option value="Jawa Timur" {{ old('domisili') == 'Jawa Timur' ? 'selected' : '' }}>Jawa Timur</option>
                                    <option value="Bali" {{ old('domisili') == 'Bali' ? 'selected' : '' }}>Bali</option>
                                    <option value="Nusa Tenggara Barat" {{ old('domisili') == 'Nusa Tenggara Barat' ? 'selected' : '' }}>Nusa Tenggara
                                        Barat</option>
                                    <option value="Nusa Tenggara Timur" {{ old('domisili') == 'Nusa Tenggara Timur' ? 'selected' : '' }}>Nusa Tenggara
                                        Timur</option>
                                    <option value="Kalimantan Barat" {{ old('domisili') == 'Kalimantan Barat' ? 'selected' : '' }}>Kalimantan Barat
                                    </option>
                                    <option value="Kalimantan Tengah" {{ old('domisili') == 'Kalimantan Tengah' ? 'selected' : '' }}>Kalimantan Tengah
                                    </option>
                                    <option value="Kalimantan Selatan" {{ old('domisili') == 'Kalimantan Selatan' ? 'selected' : '' }}>Kalimantan Selatan
                                    </option>
                                    <option value="Kalimantan Timur" {{ old('domisili') == 'Kalimantan Timur' ? 'selected' : '' }}>Kalimantan Timur
                                    </option>
                                    <option value="Kalimantan Utara" {{ old('domisili') == 'Kalimantan Utara' ? 'selected' : '' }}>Kalimantan Utara
                                    </option>
                                    <option value="Sulawesi Utara" {{ old('domisili') == 'Sulawesi Utara' ? 'selected' : '' }}>Sulawesi Utara</option>
                                    <option value="Gorontalo" {{ old('domisili') == 'Gorontalo' ? 'selected' : '' }}>Gorontalo</option>
                                    <option value="Sulawesi Tengah" {{ old('domisili') == 'Sulawesi Tengah' ? 'selected' : '' }}>Sulawesi Tengah</option>
                                    <option value="Sulawesi Barat" {{ old('domisili') == 'Sulawesi Barat' ? 'selected' : '' }}>Sulawesi Barat</option>
                                    <option value="Sulawesi Selatan" {{ old('domisili') == 'Sulawesi Selatan' ? 'selected' : '' }}>Sulawesi Selatan
                                    </option>
                                    <option value="Sulawesi Tenggara" {{ old('domisili') == 'Sulawesi Tenggara' ? 'selected' : '' }}>Sulawesi Tenggara
                                    </option>
                                    <option value="Maluku" {{ old('domisili') == 'Maluku' ? 'selected' : '' }}>Maluku</option>
                                    <option value="Maluku Utara" {{ old('domisili') == 'Maluku Utara' ? 'selected' : '' }}>Maluku Utara</option>
                                    <option value="Papua" {{ old('domisili') == 'Papua' ? 'selected' : '' }}>Papua</option>
                                    <option value="Papua Barat" {{ old('domisili') == 'Papua Barat' ? 'selected' : '' }}>Papua Barat</option>
                                    <option value="Papua Selatan" {{ old('domisili') == 'Papua Selatan' ? 'selected' : '' }}>Papua Selatan</option>
                                    <option value="Papua Tengah" {{ old('domisili') == 'Papua Tengah' ? 'selected' : '' }}>Papua Tengah</option>
                                    <option value="Papua Pegunungan" {{ old('domisili') == 'Papua Pegunungan' ? 'selected' : '' }}>Papua Pegunungan
                                    </option>
                                    <option value="Papua Barat Daya" {{ old('domisili') == 'Papua Barat Daya' ? 'selected' : '' }}>Papua Barat Daya
                                    </option>
                                </select>
                                @error('domisili')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tahun_lulus" class="form-label">Tahun Lulus</label>
                                    <input type="text" class="form-control @error('tahun_lulus') is-invalid @enderror" id="tahun_lulus"
                                        name="tahun_lulus" value="{{ old('tahun_lulus') }}" required placeholder="Contoh: 2022">
                                    @error('tahun_lulus')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="tahun_angkatan" class="form-label">Tahun Angkatan</label>
                                    <input type="text" class="form-control @error('tahun_angkatan') is-invalid @enderror" id="tahun_angkatan"
                                        name="tahun_angkatan" value="{{ old('tahun_angkatan') }}" required placeholder="Contoh: 2018">
                                    @error('tahun_angkatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="program_studi" class="form-label">Program Studi</label>
                                    <select class="form-select @error('program_studi') is-invalid @enderror" id="program_studi" name="program_studi"
                                        required>
                                        <option value="" selected disabled>Pilih Program Studi</option>
                                        <option value="S1 Informatika" {{ old('program_studi') == 'S1 Informatika' ? 'selected' : '' }}>S1
                                            Informatika</option>
                                        <option value="S1 Sains Data" {{ old('program_studi') == 'S1 Sains Data' ? 'selected' : '' }}>S1 Sains
                                            Data</option>
                                        <option value="S1 Sistem Informasi" {{ old('program_studi') == 'S1 Sistem Informasi' ? 'selected' : '' }}>
                                            S1 Sistem Informasi</option>
                                        <option value="D3 Sistem Informasi" {{ old('program_studi') == 'D3 Sistem Informasi' ? 'selected' : '' }}>
                                            D3 Sistem Informasi</option>
                                    </select>
                                    @error('program_studi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="no_telepon" class="form-label">Nomor Telepon (Opsional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+62</span>
                                        <input type="text" class="form-control @error('no_telepon') is-invalid @enderror" id="no_telepon"
                                            name="no_telepon" value="{{ old('no_telepon') }}" placeholder="8123456789" maxlength="13"
                                            pattern="[8][0-9]{8,12}" title="Nomor telepon harus dimulai dengan 8 dan memiliki 9-13 digit">
                                        @error('no_telepon')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                                    required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary py-2">Daftar</button>
                            </div>
                        </form>

                        <div class="mt-4 text-center">
                            <p>Sudah memiliki akun? <a href="{{ route('login') }}">Login di sini</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Format nomor telepon input
        document.getElementById('no_telepon').addEventListener('input', function(e) {
            // Hapus karakter non-digit
            let value = e.target.value.replace(/\D/g, '');

            // Pastikan dimulai dengan 8
            if (value.length > 0 && !value.startsWith('8')) {
                if (value.startsWith('0')) {
                    value = '8' + value.substring(1);
                } else {
                    value = '8' + value;
                }
            }

            // Batasi maksimal 13 digit
            if (value.length > 13) {
                value = value.substring(0, 13);
            }

            e.target.value = value;
        });

        // Validasi saat form submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const phoneInput = document.getElementById('no_telepon');
            const phoneValue = phoneInput.value;

            // Jika ada input nomor telepon, validasi format
            if (phoneValue && phoneValue.length > 0) {
                if (!phoneValue.startsWith('8') || phoneValue.length < 9) {
                    e.preventDefault();
                    phoneInput.classList.add('is-invalid');

                    // Hapus pesan error yang ada
                    const existingError = phoneInput.parentNode.querySelector('.invalid-feedback');
                    if (existingError) {
                        existingError.remove();
                    }

                    // Tambah pesan error baru
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = 'Nomor telepon harus dimulai dengan 8 dan minimal 9 digit';
                    phoneInput.parentNode.appendChild(errorDiv);

                    return false;
                }
            }
        });
    </script>
@endsection
