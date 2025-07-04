@extends('layouts.app')

@section('title', 'Kuesioner Kepuasan Pengguna Lulusan - SPM FIK UPNVJ')

@section('content')
    <div class="container py-5">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 fw-bold text-orange">
                    <i class="fas fa-building me-2"></i> Kuesioner Kepuasan Pengguna Lulusan
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
                        <h2 class="h4 mb-0 fw-bold">Formulir Isian Pengguna Lulusan</h2>
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
                            <p>Kami mohon kesediaan Bapak/Ibu untuk memberikan penilaian terhadap kualitas alumni FIK UPNVJ.
                                Data ini akan digunakan sebagai bahan evaluasi dan pengembangan kualitas lulusan kami.</p>
                            <ul class="mb-0">
                                <li>Silakan lengkapi data diri dan data alumni yang akan dinilai</li>
                                <li>Semua field bertanda <span class="text-danger fw-bold">*</span> wajib diisi</li>
                                <li>Setelah mengisi data diri, Anda akan diarahkan untuk mengisi kuesioner kepuasan</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <form action="{{ route('public.questionnaire.pengguna-lulusan.process', $questionnaire) }}" method="POST" id="penggunaLulusanForm">
                    @csrf

                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="border-bottom pb-2 mb-3 text-orange">Data Penilai</h4>
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
                            <label for="nama_instansi" class="form-label fw-medium">Nama Perusahaan/Instansi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-xl @error('nama_instansi') is-invalid @enderror" id="nama_instansi"
                                name="nama_instansi" value="{{ old('nama_instansi') }}" required>
                            @error('nama_instansi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="border-bottom pb-2 mb-3 text-orange">Data Alumni yang Dinilai</h4>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nama_alumni" class="form-label fw-medium">Nama Alumni FIK UPNVJ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-xl @error('nama_alumni') is-invalid @enderror" id="nama_alumni"
                                name="nama_alumni" value="{{ old('nama_alumni') }}" required>
                            @error('nama_alumni')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tahun_lulus_alumni" class="form-label fw-medium">Tahun Lulus <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-xl @error('tahun_lulus_alumni') is-invalid @enderror"
                                id="tahun_lulus_alumni" name="tahun_lulus_alumni" value="{{ old('tahun_lulus_alumni') }}" required
                                placeholder="Contoh: 2022">
                            @error('tahun_lulus_alumni')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="program_studi_alumni" class="form-label fw-medium">Program Studi <span class="text-danger">*</span></label>
                            <select class="form-select rounded-xl @error('program_studi_alumni') is-invalid @enderror" id="program_studi_alumni"
                                name="program_studi_alumni" required>
                                <option value="" selected disabled>Pilih Program Studi</option>
                                <option value="S1 Informatika" {{ old('program_studi_alumni') == 'S1 Informatika' ? 'selected' : '' }}>S1 Informatika
                                </option>
                                <option value="S1 Sains Data" {{ old('program_studi_alumni') == 'S1 Sains Data' ? 'selected' : '' }}>S1 Sains Data
                                </option>
                                <option value="S1 Sistem Informasi" {{ old('program_studi_alumni') == 'S1 Sistem Informasi' ? 'selected' : '' }}>S1
                                    Sistem Informasi</option>
                                <option value="D3 Sistem Informasi" {{ old('program_studi_alumni') == 'D3 Sistem Informasi' ? 'selected' : '' }}>D3
                                    Sistem Informasi</option>
                            </select>
                            @error('program_studi_alumni')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('home') }}" class="btn btn-outline-orange px-4 rounded-xl">
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
            document.getElementById('penggunaLulusanForm').addEventListener('submit', function(e) {
                // Tambahkan validasi jika diperlukan
            });
        });
    </script>
@endsection
