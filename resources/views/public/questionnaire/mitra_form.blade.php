@extends('layouts.app')

@section('title', 'Kuesioner Kepuasan Mitra - SPM FIK UPNVJ')

@section('content')
    <div class="container py-5">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 fw-bold text-orange">
                    <i class="fas fa-handshake me-2"></i> Kuesioner Kepuasan Mitra
                </h1>
                <p class="text-muted mb-0">
                    Fakultas Ilmu Komputer, UPN "Veteran" Jakarta
                </p>
            </div>
            <div class="mt-3 mt-md-0">
                <span class="badge bg-orange text-white px-3 py-2 rounded-xl">
                    <i class="far fa-clock me-1"></i>
                    Periode: {{ $activePeriod->name }}
                </span>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card border-0 shadow-orange rounded-xl overflow-hidden">
            <!-- Card Header -->
            <div class="card-header bg-orange text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="h4 mb-0 fw-bold">Formulir Isian Mitra Kerjasama</h2>
                        <p class="mb-0 opacity-75">Silakan lengkapi data berikut untuk melanjutkan ke kuesioner</p>
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="card-body p-4">
                <!-- Instructions -->
                <div class="alert bg-orange-light border-orange text-orange rounded-xl">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle fs-4 mt-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="alert-heading fw-bold">Petunjuk Pengisian</h5>
                            <p>Kami mohon kesediaan Bapak/Ibu untuk memberikan penilaian terhadap layanan kerjasama FIK UPNVJ.
                                Data ini akan digunakan sebagai bahan evaluasi dan peningkatan mutu layanan kami.</p>
                            <ul class="mb-0">
                                <li>Silakan lengkapi data diri dan informasi kerjasama</li>
                                <li>Semua field bertanda <span class="text-danger fw-bold">*</span> wajib diisi</li>
                                <li>Setelah mengisi data, Anda akan diarahkan untuk mengisi kuesioner kepuasan</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <form action="{{ route('public.questionnaire.mitra.process', $questionnaire) }}" method="POST" id="mitraForm">
                    @csrf

                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="border-bottom pb-2 mb-3 text-orange">Data Responden</h4>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label fw-medium">Nama Responden <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-xl @error('name') is-invalid @enderror" id="name" name="name"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="jabatan" class="form-label fw-medium">Jabatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-xl @error('jabatan') is-invalid @enderror" id="jabatan" name="jabatan"
                                value="{{ old('jabatan') }}" required>
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nama_instansi" class="form-label fw-medium">Nama Institusi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-xl @error('nama_instansi') is-invalid @enderror" id="nama_instansi"
                                name="nama_instansi" value="{{ old('nama_instansi') }}" required>
                            @error('nama_instansi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="border-bottom pb-2 mb-3 text-orange">Informasi Kerjasama</h4>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="jenis_mitra" class="form-label fw-medium">Jenis Mitra <span class="text-danger">*</span></label>
                            <select class="form-select rounded-xl @error('jenis_mitra') is-invalid @enderror" id="jenis_mitra" name="jenis_mitra"
                                required>
                                <option value="" selected disabled>Pilih Jenis Mitra</option>
                                <option value="Perusahaan Multinasional" {{ old('jenis_mitra') == 'Perusahaan Multinasional' ? 'selected' : '' }}>
                                    Perusahaan Multinasional</option>
                                <option value="Perusahaan Nasional Berstandar Tinggi, BUMN, dan/ atau BUMD"
                                    {{ old('jenis_mitra') == 'Perusahaan Nasional Berstandar Tinggi, BUMN, dan/ atau BUMD' ? 'selected' : '' }}>
                                    Perusahaan Nasional Berstandar Tinggi, BUMN, dan/ atau BUMD</option>
                                <option value="Perusahaan Teknologi Global" {{ old('jenis_mitra') == 'Perusahaan Teknologi Global' ? 'selected' : '' }}>
                                    Perusahaan Teknologi Global</option>
                                <option value="Perusahaan Rintisan (Startup Company) Teknologi"
                                    {{ old('jenis_mitra') == 'Perusahaan Rintisan (Startup Company) Teknologi' ? 'selected' : '' }}>
                                    Perusahaan Rintisan (Startup Company) Teknologi</option>
                                <option value="Organisasi Nirlaba Kelas Dunia"
                                    {{ old('jenis_mitra') == 'Organisasi Nirlaba Kelas Dunia' ? 'selected' : '' }}>
                                    Organisasi Nirlaba Kelas Dunia</option>
                                <option value="Institusi/Organisasi Multilateral"
                                    {{ old('jenis_mitra') == 'Institusi/Organisasi Multilateral' ? 'selected' : '' }}>
                                    Institusi/Organisasi Multilateral</option>
                                <option value="Perguruan Tinggi Yang Masuk Dalam Daftar QS200 Berdasarkan Bidang Ilmu (QS200 By Subject)"
                                    {{ old('jenis_mitra') == 'Perguruan Tinggi Yang Masuk Dalam Daftar QS200 Berdasarkan Bidang Ilmu (QS200 By Subject)' ? 'selected' : '' }}>
                                    Perguruan Tinggi Yang Masuk Dalam Daftar QS200 Berdasarkan Bidang Ilmu (QS200 By Subject)</option>
                                <option value="Perguruan Tinggi Dalam Negeri Yang Masuk Dalam Daftar QS200 Berdasarkan Bidang Ilmu (QS200 By Subject)"
                                    {{ old('jenis_mitra') == 'Perguruan Tinggi Dalam Negeri Yang Masuk Dalam Daftar QS200 Berdasarkan Bidang Ilmu (QS200 By Subject)' ? 'selected' : '' }}>
                                    Perguruan Tinggi Dalam Negeri Yang Masuk Dalam Daftar QS200 Berdasarkan Bidang Ilmu (QS200 By Subject)</option>
                                <option value="Instansi Pemerintah (Pusat/Daerah)"
                                    {{ old('jenis_mitra') == 'Instansi Pemerintah (Pusat/Daerah)' ? 'selected' : '' }}>
                                    Instansi Pemerintah (Pusat/Daerah)</option>
                                <option value="Rumah Sakit" {{ old('jenis_mitra') == 'Rumah Sakit' ? 'selected' : '' }}>
                                    Rumah Sakit</option>
                                <option value="Lembaga Riset Pemerintah, Swasta, Nasional, Maupun Internasional"
                                    {{ old('jenis_mitra') == 'Lembaga Riset Pemerintah, Swasta, Nasional, Maupun Internasional' ? 'selected' : '' }}>
                                    Lembaga Riset Pemerintah, Swasta, Nasional, Maupun Internasional</option>
                                <option value="Lembaga Kebudayaan Berskala Nasional / Bereputasi"
                                    {{ old('jenis_mitra') == 'Lembaga Kebudayaan Berskala Nasional / Bereputasi' ? 'selected' : '' }}>
                                    Lembaga Kebudayaan Berskala Nasional / Bereputasi</option>
                                <option value="PTN/PTS (Tidak Termasuk QS200)"
                                    {{ old('jenis_mitra') == 'PTN/PTS (Tidak Termasuk QS200)' ? 'selected' : '' }}>
                                    PTN/PTS (Tidak Termasuk QS200)</option>
                                <option value="Dunia Usaha / Dunia Industri (selain kategori diatas)"
                                    {{ old('jenis_mitra') == 'Dunia Usaha / Dunia Industri (selain kategori diatas)' ? 'selected' : '' }}>
                                    Dunia Usaha / Dunia Industri (selain kategori diatas)</option>
                            </select>
                            @error('jenis_mitra')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="jenis_kerjasama" class="form-label fw-medium">Jenis Kerjasama <span class="text-danger">*</span></label>
                            <select class="form-select rounded-xl @error('jenis_kerjasama') is-invalid @enderror" id="jenis_kerjasama"
                                name="jenis_kerjasama" required>
                                <option value="" selected disabled>Pilih Jenis Kerjasama</option>
                                <option value="MoU" {{ old('jenis_kerjasama') == 'MoU' ? 'selected' : '' }}>MoU</option>
                                <option value="MoA" {{ old('jenis_kerjasama') == 'MoA' ? 'selected' : '' }}>MoA</option>
                                <option value="IA" {{ old('jenis_kerjasama') == 'IA' ? 'selected' : '' }}>IA</option>
                            </select>
                            @error('jenis_kerjasama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="lingkup_kerjasama" class="form-label fw-medium">Lingkup Kerjasama <span class="text-danger">*</span></label>
                            <select class="form-select rounded-xl @error('lingkup_kerjasama') is-invalid @enderror" id="lingkup_kerjasama"
                                name="lingkup_kerjasama" required>
                                <option value="" selected disabled>Pilih Lingkup Kerjasama</option>
                                <option value="Pendidikan" {{ old('lingkup_kerjasama') == 'Pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                                <option value="Penelitian" {{ old('lingkup_kerjasama') == 'Penelitian' ? 'selected' : '' }}>Penelitian</option>
                                <option value="Pengabdian Masyarakat" {{ old('lingkup_kerjasama') == 'Pengabdian Masyarakat' ? 'selected' : '' }}>
                                    Pengabdian Masyarakat</option>
                                <option value="MBKM" {{ old('lingkup_kerjasama') == 'MBKM' ? 'selected' : '' }}>MBKM</option>
                                <option value="Lainnya" {{ old('lingkup_kerjasama') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('lingkup_kerjasama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="periode_kerjasama" class="form-label fw-medium">Periode Kerjasama <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-xl @error('periode_kerjasama') is-invalid @enderror"
                                id="periode_kerjasama" name="periode_kerjasama" value="{{ old('periode_kerjasama') }}" required
                                placeholder="Contoh: 2022-2025 / Januari 2022 - Desember 2025">
                            @error('periode_kerjasama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="no_telepon" class="form-label fw-medium">No Telepon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-xl @error('no_telepon') is-invalid @enderror" id="no_telepon"
                                name="no_telepon" value="{{ old('no_telepon') }}" required>
                            @error('no_telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="alamat" class="form-label fw-medium">Alamat <span class="text-danger">*</span></label>
                            <textarea class="form-control rounded-xl @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3" required>{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('home') }}" class="btn btn-outline-primary px-4 rounded-xl">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary px-4 rounded-xl">
                            <i class="fas fa-arrow-right me-2"></i> Lanjut ke Kuesioner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(255, 107, 53, 0.25);
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validasi form sebelum submit
            document.getElementById('mitraForm').addEventListener('submit', function(e) {
                // Tambahkan validasi jika diperlukan
            });
        });
    </script>
@endsection
