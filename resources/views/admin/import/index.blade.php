@extends('layouts.admin')

@section('title', 'Import Data - SPM FIK UPNVJ')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Import Hasil Kuesioner</h1>
                <p class="mb-0 text-muted">Import data hasil kuesioner dari Google Form atau Excel</p>
            </div>
        </div>

        <!-- Main Import Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3" style="background-color: rgb(184, 68, 0);">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-file-import me-2"></i>Form Import Data
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.import.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- Pilih Kuesioner -->
                        <div class="col-md-6 mb-3">
                            <label for="questionnaire_id" class="form-label fw-bold">
                                <i class="fas fa-clipboard-list text-primary me-1"></i>Pilih Kuesioner
                            </label>
                            <select name="questionnaire_id" id="questionnaire_id" class="form-select" required onchange="updateDownloadLink()">
                                <option value="">-- Pilih Kuesioner --</option>
                                @foreach (\App\Models\Questionnaire::with('academicPeriod')->get() as $questionnaire)
                                    <option value="{{ $questionnaire->id }}" data-type="{{ $questionnaire->type }}">
                                        {{ $questionnaire->title }} ({{ $questionnaire->academicPeriod->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Upload File -->
                        <div class="col-md-6 mb-3">
                            <label for="excel_file" class="form-label fw-bold">
                                <i class="fas fa-file-excel text-success me-1"></i>File Excel/CSV
                            </label>
                            <input type="file" name="excel_file" id="excel_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                            <small class="text-muted">Format: .xlsx, .xls, .csv (maksimal 10MB)</small>
                        </div>
                    </div>

                    <!-- Download Template Section -->
                    <div class="row" id="download-template" style="display: none;">
                        <div class="col-12 mb-3">
                            <div class="alert alert-info border-left-info">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-download fa-2x text-info me-3"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Download Template & Data</h6>
                                        <p class="mb-2 small">Pilih salah satu opsi untuk memulai import data</p>
                                        <a href="#" id="download-results-link" class="btn btn-info btn-sm">
                                            <i class="fas fa-database me-1"></i>Data Existing
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Format Info -->
                    <div id="format-info" class="row mb-3" style="display: none;">
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <h6 class="mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Format Khusus Detected!</h6>
                                <div id="format-details"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-upload me-2"></i>Import Data
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Instructions Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>Petunjuk Format File
                </h6>
            </div>
            <div class="card-body">
                <!-- Format Tabs -->
                <ul class="nav nav-tabs" id="formatTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="standard-tab" data-bs-toggle="tab" data-bs-target="#standard" type="button" role="tab">
                            Format Standar
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="mitra-tab" data-bs-toggle="tab" data-bs-target="#mitra" type="button" role="tab">
                            Kepuasan Mitra
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="formatTabsContent">
                    <!-- Standard Format -->
                    <div class="tab-pane fade show active" id="standard" role="tabpanel">
                        <h6 class="text-dark mb-3">Struktur Kolom Standar (Mahasiswa, Dosen, Tendik, Alumni, dll):</h6>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item border-0 px-0 py-1">
                                        <span class="badge bg-primary me-2">1</span><strong>NIM</strong> - Untuk mahasiswa
                                    </div>
                                    <div class="list-group-item border-0 px-0 py-1">
                                        <span class="badge bg-primary me-2">2</span><strong>NIP</strong> - Untuk dosen/tendik
                                    </div>
                                    <div class="list-group-item border-0 px-0 py-1">
                                        <span class="badge bg-primary me-2">3</span><strong>NIK</strong> - Untuk alumni
                                    </div>
                                    <div class="list-group-item border-0 px-0 py-1">
                                        <span class="badge bg-primary me-2">4</span><strong>Nama</strong> - Nama responden
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item border-0 px-0 py-1">
                                        <span class="badge bg-primary me-2">5</span><strong>Email</strong> - Email responden
                                    </div>
                                    <div class="list-group-item border-0 px-0 py-1">
                                        <span class="badge bg-primary me-2">6</span><strong>Program Studi</strong> - Prodi
                                    </div>
                                    <div class="list-group-item border-0 px-0 py-1">
                                        <span class="badge bg-primary me-2">7</span><strong>No Telepon</strong> - Kontak
                                    </div>
                                    <div class="list-group-item border-0 px-0 py-1">
                                        <span class="badge bg-primary me-2">8</span><strong>Field Tambahan</strong> - Sesuai role
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="badge bg-success me-2">9+</span><strong>Jawaban Pertanyaan</strong> - Rating 1-4 untuk setiap pertanyaan
                        </div>
                    </div>

                    <!-- Mitra Format -->
                    <div class="tab-pane fade" id="mitra" role="tabpanel">
                        <h6 class="text-dark mb-3">Struktur Kolom Kepuasan Mitra Kerjasama:</h6>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item border-0 px-0 py-1">
                                        <span class="badge bg-danger me-2">1</span><strong>Nama Responden</strong> - Wajib
                                    </div>
                                    <div class="list-group-item border-0 px-0 py-1">
                                        <span class="badge bg-danger me-2">2</span><strong>Email</strong> - Wajib & Valid
                                    </div>
                                    <div class="list-group-item border-0 px-0 py-1">
                                        <span class="badge bg-warning me-2">3</span><strong>Nama Institusi</strong> - Perusahaan/Organisasi
                                    </div>
                                    <div class="list-group-item border-0 px-0 py-1">
                                        <span class="badge bg-warning me-2">4</span><strong>Jabatan</strong> - Posisi di institusi
                                    </div>
                                    <div class="list-group-item border-0 px-0 py-1">
                                        <span class="badge bg-info me-2">5</span><strong>Jenis Mitra</strong> - Industri/UMKM/Pemerintah
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item border-0 px-0 py-1">
                                        <span class="badge bg-info me-2">6</span><strong>Jenis Kerjasama</strong> - Pendidikan/Penelitian/PKL/dll
                                    </div>
                                    <div class="list-group-item border-0 px-0 py-1">
                                        <span class="badge bg-info me-2">7</span><strong>Lingkup Kerjasama</strong> - Magang/Penelitian/dll
                                    </div>
                                    <div class="list-group-item border-0 px-0 py-1">
                                        <span class="badge bg-info me-2">8</span><strong>Periode Kerjasama</strong> - 2024-2025
                                    </div>
                                    <div class="list-group-item border-0 px-0 py-1">
                                        <span class="badge bg-secondary me-2">9</span><strong>No Telepon</strong> - Kontak
                                    </div>
                                    <div class="list-group-item border-0 px-0 py-1">
                                        <span class="badge bg-secondary me-2">10</span><strong>Alamat</strong> - Alamat lengkap
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="row">
                                <div class="col-lg-4">
                                    <span class="badge bg-success me-2">11-20</span><strong>Pertanyaan 1-10</strong> - Rating 1-4
                                </div>
                                <div class="col-lg-4">
                                    <span class="badge bg-purple me-2">21</span><strong>Saran dari Mitra</strong> - Text bebas
                                </div>
                                <div class="col-lg-4">
                                    <span class="badge bg-purple me-2">22</span><strong>Saran Kemajuan FIK</strong> - Text bebas
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role-based Requirements -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="text-dark mb-3">Field Wajib Berdasarkan Role:</h6>
                    </div>
                </div>

                <div class="row">
                    <!-- Mahasiswa -->
                    <div class="col-lg-6 mb-3">
                        <div class="card border-left-success h-100">
                            <div class="card-body py-3">
                                <h6 class="text-success"><i class="fas fa-graduation-cap me-2"></i>Mahasiswa</h6>
                                <small class="text-muted">
                                    <i class="fas fa-check text-success me-1"></i><strong>Wajib:</strong> NIM, Nama, Email, Prodi, Telepon<br>
                                    <i class="fas fa-times text-danger me-1"></i><strong>Kosong:</strong> NIP, NIK, Field Tambahan
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Dosen -->
                    <div class="col-lg-6 mb-3">
                        <div class="card border-left-info h-100">
                            <div class="card-body py-3">
                                <h6 class="text-info"><i class="fas fa-chalkboard-teacher me-2"></i>Dosen</h6>
                                <small class="text-muted">
                                    <i class="fas fa-check text-success me-1"></i><strong>Wajib:</strong> NIP, Nama, Email, Prodi, Telepon<br>
                                    <i class="fas fa-times text-danger me-1"></i><strong>Kosong:</strong> NIM, NIK, Field Tambahan
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Alumni -->
                    <div class="col-lg-6 mb-3">
                        <div class="card border-left-primary h-100">
                            <div class="card-body py-3">
                                <h6 class="text-primary"><i class="fas fa-user-graduate me-2"></i>Alumni</h6>
                                <small class="text-muted">
                                    <i class="fas fa-check text-success me-1"></i><strong>Wajib:</strong> NIK, Nama, Email, Prodi, Telepon<br>
                                    <i class="fas fa-plus text-info me-1"></i><strong>Field Tambahan:</strong> TahunLulus|Domisili|NPWP
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Mitra -->
                    <div class="col-lg-6 mb-3">
                        <div class="card border-left-danger h-100">
                            <div class="card-body py-3">
                                <h6 class="text-danger"><i class="fas fa-handshake me-2"></i>Mitra Kerjasama</h6>
                                <small class="text-muted">
                                    <i class="fas fa-exclamation text-warning me-1"></i><strong>Format Khusus:</strong> 10 kolom data + pertanyaan + 2
                                    saran<br>
                                    <i class="fas fa-info text-info me-1"></i><strong>Detail:</strong> Lihat tab "Kepuasan Mitra" di atas
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rating Format -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-light border">
                            <h6 class="text-dark mb-2"><i class="fas fa-star text-warning me-2"></i>Format Rating Jawaban:</h6>
                            <div class="row text-center">
                                <div class="col-3">
                                    <span class="badge bg-danger fs-6">1</span><br>
                                    <small>Kurang/Tidak Puas</small>
                                </div>
                                <div class="col-3">
                                    <span class="badge bg-warning fs-6">2</span><br>
                                    <small>Cukup</small>
                                </div>
                                <div class="col-3">
                                    <span class="badge bg-primary fs-6">3</span><br>
                                    <small>Baik/Puas</small>
                                </div>
                                <div class="col-3">
                                    <span class="badge bg-success fs-6">4</span><br>
                                    <small>Sangat Baik/Puas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Example Format Cards -->
        <div class="row">
            <!-- Standard Example -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-table me-2"></i>Contoh Format Standar
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>NIM</th>
                                        <th>NIP</th>
                                        <th>NIK</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Prodi</th>
                                        <th>Telepon</th>
                                        <th>Extra</th>
                                        <th>P1</th>
                                        <th>P2</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge bg-success">2023001</span></td>
                                        <td class="text-muted">-</td>
                                        <td class="text-muted">-</td>
                                        <td>Ahmad Fauzi</td>
                                        <td>ahmad@student.upnvj.ac.id</td>
                                        <td>S1 Informatika</td>
                                        <td>081234567890</td>
                                        <td class="text-muted">-</td>
                                        <td><span class="badge bg-success">4</span></td>
                                        <td><span class="badge bg-primary">3</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mitra Example -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-danger">
                            <i class="fas fa-handshake me-2"></i>Contoh Format Mitra
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Institusi</th>
                                        <th>Jabatan</th>
                                        <th>Jenis Mitra</th>
                                        <th>Jenis Kerjasama</th>
                                        <th>Lingkup</th>
                                        <th>Periode</th>
                                        <th>Telepon</th>
                                        <th>Alamat</th>
                                        <th>P1</th>
                                        <th>P2</th>
                                        <th>Saran Mitra</th>
                                        <th>Saran FIK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Ahmad Partner</td>
                                        <td>partner@company.com</td>
                                        <td>PT ABC Tech</td>
                                        <td>Manager</td>
                                        <td>Industri</td>
                                        <td>Pendidikan & Pelatihan</td>
                                        <td>Magang & PKL</td>
                                        <td>2024-2025</td>
                                        <td>081234567890</td>
                                        <td>Jakarta</td>
                                        <td><span class="badge bg-success">4</span></td>
                                        <td><span class="badge bg-primary">3</span></td>
                                        <td>Pelayanan baik</td>
                                        <td>Tingkatkan kualitas</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="card shadow">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-dark">Catatan Penting:</h6>
                        <ul class="list-unstyled small text-muted">
                            <li><i class="fas fa-key text-warning me-1"></i>Username dibuat otomatis</li>
                            <li><i class="fas fa-lock text-info me-1"></i>Password default: "password"</li>
                            <li><i class="fas fa-envelope text-primary me-1"></i>Email harus valid dan unik</li>
                            <li><i class="fas fa-exclamation text-danger me-1"></i>Format mitra: 10 kolom data!</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-dark">Format Khusus:</h6>
                        <ul class="list-unstyled small text-muted">
                            <li><i class="fas fa-minus text-secondary me-1"></i>Kolom kosong: gunakan "-"</li>
                            <li><i class="fas fa-separator text-info me-1"></i>CSV: gunakan titik koma (;)</li>
                            <li><i class="fas fa-ban text-danger me-1"></i>Sistem selalu buat user baru</li>
                            <li><i class="fas fa-comment text-success me-1"></i>Saran: Text bebas (mitra only)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateDownloadLink() {
            const select = document.getElementById('questionnaire_id');
            const downloadDiv = document.getElementById('download-template');
            const downloadResultsLink = document.getElementById('download-results-link');
            const formatInfo = document.getElementById('format-info');
            const formatDetails = document.getElementById('format-details');

            if (select.value) {
                const selectedOption = select.options[select.selectedIndex];
                const questionnaireType = selectedOption.dataset.type;

                downloadResultsLink.href = '{{ route('admin.import.download-results') }}?questionnaire_id=' + select.value;
                downloadDiv.style.display = 'block';

                // Show format info for special types
                if (questionnaireType === 'kepuasan_mitra') {
                    formatInfo.style.display = 'block';
                    formatDetails.innerHTML = `
                        <strong>Kuesioner Kepuasan Mitra</strong> menggunakan format khusus dengan 10 kolom data + pertanyaan + 2 kolom saran.<br>
                        <i class="fas fa-info-circle text-info me-1"></i>Pastikan file Anda mengikuti format pada tab "Kepuasan Mitra" di bawah.
                    `;
                } else {
                    formatInfo.style.display = 'none';
                }
            } else {
                downloadDiv.style.display = 'none';
                formatInfo.style.display = 'none';
            }
        }

        // Bootstrap 5 tab initialization
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips if any
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    <style>
        .border-left-success {
            border-left: 4px solid #28a745 !important;
        }

        .border-left-info {
            border-left: 4px solid #17a2b8 !important;
        }

        .border-left-primary {
            border-left: 4px solid #007bff !important;
        }

        .border-left-danger {
            border-left: 4px solid #dc3545 !important;
        }

        .border-left-warning {
            border-left: 4px solid #ffc107 !important;
        }

        .bg-purple {
            background-color: #6f42c1 !important;
        }

        .text-purple {
            color: #6f42c1 !important;
        }

        .table th {
            font-size: 0.8rem;
            white-space: nowrap;
        }

        .table td {
            font-size: 0.75rem;
        }
    </style>
@endsection
