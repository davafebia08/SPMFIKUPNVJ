@extends('layouts.app')

@section('title', 'Selamat Datang - SPM FIK UPNVJ')

@section('content')
    <!-- Hero Section -->
    <div class="row align-items-center mb-5">
        <div class="col-lg-6 mb-5 mb-lg-0">
            <h1 class="display-4 fw-bold mb-3">Sistem Pelayanan Minimal</h1>
            <h2 class="h3 mb-4">Fakultas Ilmu Komputer UPN "Veteran" Jakarta</h2>
            <p class="lead mb-4">
                Sistem informasi yang dirancang untuk meningkatkan kualitas layanan melalui evaluasi dan
                monitoring berbagai aspek pelayanan di Fakultas Ilmu Komputer.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4 rounded-xl">
                    <i class="fas fa-sign-in-alt me-2"></i> Login
                </a>
                <a href="{{ route('public.questionnaire.index') }}" class="btn btn-outline-danger btn-lg px-4 rounded-xl">
                    <i class="fas fa-clipboard-list me-2"></i> Isi Kuesioner
                </a>
            </div>
        </div>
        <div class="col-lg-6 text-center">
            <img src="{{ asset('images/Background_Welcome.jpg') }}" alt="Logo FIK UPNVJ" class="img-fluid rounded-xl" style="max-height: 400px;">
        </div>
    </div>

    <!-- Kartu Nilai Total SPM -->
    @php
        $averageSPM = $dashboardData['average_spm'] ?? 0;

        // Tentukan kategori dan warna
        if ($averageSPM >= 3.5) {
            $spmCategory = 'Sangat Baik';
            $spmColor = 'success';
        } elseif ($averageSPM >= 3.0) {
            $spmCategory = 'Baik';
            $spmColor = 'primary';
        } elseif ($averageSPM >= 2.0) {
            $spmCategory = 'Cukup';
            $spmColor = 'warning';
        } else {
            $spmCategory = 'Kurang';
            $spmColor = 'danger';
        }

        $count = count($dashboardData['questionnaire_types'] ?? []);
    @endphp

    <div class="card mb-4 shadow-orange">
        <div class="card-header bg-orange text-white rounded-top-3">
            <h5 class="mb-0">Nilai Total SPM FIK UPNVJ</h5>
        </div>
        <div class="card-body text-center bg-orange-light rounded-bottom-3">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-around">
                <div class="mb-3 mb-md-0">
                    <h2 class="display-4 fw-bold text-{{ $spmColor }}">{{ number_format($averageSPM, 2) }}</h2>
                    <span class="badge bg-{{ $spmColor }} fs-6">{{ $spmCategory }}</span>
                </div>
                <div class="text-md-start">
                    <p class="mb-1"><strong>Periode:</strong> {{ $dashboardData['active_period'] ?? date('Y') . '/' . (date('Y') + 1) }}</p>
                    <p class="mb-1"><strong>Jumlah Kuesioner:</strong> {{ $dashboardData['total_questionnaires'] }} buah</p>
                    <p class="mb-0"><strong>Total Responden:</strong> {{ $dashboardData['total_respondents'] }} orang</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Keterangan Kategori Nilai -->
    <div class="card mb-4 shadow-orange">
        <div class="card-header bg-orange text-white rounded-top-3">
            <h5 class="mb-0">Keterangan Kategori Nilai</h5>
        </div>
        <div class="card-body bg-orange-light rounded-bottom-3">
            <div class="row text-center">
                <div class="col-md-3 mb-2">
                    <div class="p-3 bg-danger text-white rounded-3 h-100">
                        <div class="h5 fw-bold mb-1">Kurang</div>
                        <div class="fs-6">&lt; 2.0</div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="p-3 bg-warning text-white rounded-3 h-100">
                        <div class="h5 fw-bold mb-1">Cukup</div>
                        <div class="fs-6">≥ 2.0 &lt; 3.0</div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="p-3 bg-primary text-white rounded-3 h-100">
                        <div class="h5 fw-bold mb-1">Baik</div>
                        <div class="fs-6">≥ 3.0 - &lt; 3.5</div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="p-3 bg-success text-white rounded-3 h-100">
                        <div class="h5 fw-bold mb-1">Sangat Baik</div>
                        <div class="fs-6">≥ 3.5</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik dan Aspek -->
    <div class="row mb-4">
        <!-- Grafik Radar -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-orange h-100">
                <div class="card-header bg-orange text-white rounded-top-3">
                    <h5 class="mb-0">Grafik Radar Semua Kuesioner</h5>
                </div>
                <div class="card-body bg-orange-light rounded-bottom-3" style="min-height: 400px">
                    <div style="width: 100%; height: 350px; position: relative;">
                        <canvas id="radar-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aspek-Aspek Nilai -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-orange h-100">
                <div class="card-header bg-orange text-white rounded-top-3">
                    <h5 class="mb-0">Nilai per Aspek</h5>
                </div>
                <div class="card-body bg-orange-light rounded-bottom-3">
                    @foreach ($dashboardData['category_averages'] ?? [] as $category => $data)
                        @php
                            $value = $data['average'] ?? 0;

                            if ($value >= 3.5) {
                                $categoryClass = 'success';
                            } elseif ($value >= 3.0) {
                                $categoryClass = 'primary';
                            } elseif ($value >= 2.0) {
                                $categoryClass = 'warning';
                            } else {
                                $categoryClass = 'danger';
                            }

                            $percentage = min(100, ($value / 4) * 100);
                        @endphp

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1">{{ $category }}</h6>
                                <span class="badge bg-{{ $categoryClass }}">{{ number_format($value, 2) }}</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-{{ $categoryClass }}" role="progressbar" style="width: {{ $percentage }}%"
                                    aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="4">
                                </div>
                            </div>
                            <div class="small text-end mt-1">{{ $data['category'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Jenis Kuesioner Section -->
    <div class="card mb-5 shadow-orange">
        <div class="card-header bg-orange text-white rounded-top-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Hasil per Jenis Kuesioner</h5>
            <span class="badge bg-light text-orange">
                <i class="fas fa-info-circle me-1"></i> Klik untuk detail
            </span>
        </div>
        <div class="card-body p-0">
            <div class="row g-0">
                @php
                    $types = [
                        [
                            'id' => 'layanan-fakultas',
                            'type' => 'layanan_fakultas',
                            'title' => 'Kepuasan Layanan Fakultas',
                            'icon' => 'fas fa-building',
                        ],
                        ['id' => 'elom', 'type' => 'elom', 'title' => 'ELOM', 'icon' => 'fas fa-user-graduate'],
                        ['id' => 'evaluasi-dosen', 'type' => 'evaluasi_dosen', 'title' => 'Evaluasi Dosen', 'icon' => 'fas fa-chalkboard-teacher'],
                        ['id' => 'elta', 'type' => 'elta', 'title' => 'ELTA', 'icon' => 'fas fa-file-alt'],
                        ['id' => 'kepuasan-dosen', 'type' => 'kepuasan_dosen', 'title' => 'Kepuasan Dosen', 'icon' => 'fas fa-user-tie'],
                        ['id' => 'kepuasan-tendik', 'type' => 'kepuasan_tendik', 'title' => 'Tenaga Kependidikan', 'icon' => 'fas fa-users'],
                        ['id' => 'kepuasan-alumni', 'type' => 'kepuasan_alumni', 'title' => 'Kepuasan Alumni', 'icon' => 'fas fa-user-graduate'],
                        [
                            'id' => 'kepuasan-pengguna',
                            'type' => 'kepuasan_pengguna_lulusan',
                            'title' => 'Pengguna Lulusan',
                            'icon' => 'fas fa-briefcase',
                        ],
                        ['id' => 'kepuasan-mitra', 'type' => 'kepuasan_mitra', 'title' => 'Kepuasan Mitra', 'icon' => 'fas fa-handshake'],
                    ];
                @endphp

                @foreach ($types as $item)
                    @php
                        $data = $dashboardData['questionnaire_types'][$item['type']] ?? null;
                        $average = $data['average'] ?? 0;

                        if ($average >= 3.5) {
                            $typeClass = 'success';
                        } elseif ($average >= 3.0) {
                            $typeClass = 'primary';
                        } elseif ($average >= 2.0) {
                            $typeClass = 'warning';
                        } else {
                            $typeClass = 'danger';
                        }
                    @endphp

                    <div class="col-md-4 bg-orange-light border border-white">
                        <a href="{{ route('public.questionnaire.details', $item['type']) }}" class="text-decoration-none questionnaire-card-link">
                            <div class="d-flex align-items-center p-3">
                                <div class="rounded-circle bg-{{ $typeClass }} text-white p-2 d-flex justify-content-center align-items-center me-3"
                                    style="width: 40px; height: 40px;">
                                    <i class="{{ $item['icon'] }}"></i>
                                </div>
                                <div>
                                    <div class="small fw-semibold text-muted">{{ $item['title'] }}</div>
                                    <div class="d-flex align-items-center">
                                        <div class="h5 mb-0 fw-bold text-{{ $typeClass }}">{{ number_format($average, 2) }}</div>
                                        <span class="badge bg-{{ $typeClass }} ms-2 small">{{ $data['category'] ?? '' }}</span>
                                    </div>
                                </div>
                                <div class="ms-auto view-details-badge">
                                    <span class="badge bg-light text-orange rounded-pill">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>


    <!-- Features Section -->
    <div class="card border-0 shadow-orange rounded-xl mb-5 bg-orange-light">
        <div class="card-header bg-orange text-white rounded-top-3">
            <h5 class="mb-0">Jenis Kuesioner</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-xl hover-card">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-orange-light p-3 d-inline-flex mb-3">
                                <i class="fas fa-users fa-2x text-orange"></i>
                            </div>
                            <h3 class="h5 fw-bold">Evaluasi Layanan Fakultas</h3>
                            <p class="text-muted mb-0">
                                Evaluasi umum terhadap layanan yang diberikan oleh fakultas kepada semua stakeholder
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-xl hover-card">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-orange-light p-3 d-inline-flex mb-3">
                                <i class="fas fa-user-graduate fa-2x text-orange"></i>
                            </div>
                            <h3 class="h5 fw-bold">Kepuasan Layanan Mahasiswa</h3>
                            <p class="text-muted mb-0">
                                Evaluasi terhadap kualitas layanan akademik dan non-akademik kepada mahasiswa
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-xl hover-card">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-orange-light p-3 d-inline-flex mb-3">
                                <i class="fas fa-chalkboard-teacher fa-2x text-orange"></i>
                            </div>
                            <h3 class="h5 fw-bold">Evaluasi Dosen</h3>
                            <p class="text-muted mb-0">
                                Penilaian terhadap kinerja dosen dalam kegiatan pembelajaran dan pendampingan
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-xl hover-card">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-orange-light p-3 d-inline-flex mb-3">
                                <i class="fas fa-graduation-cap fa-2x text-orange"></i>
                            </div>
                            <h3 class="h5 fw-bold">Layanan Tugas Akhir</h3>
                            <p class="text-muted mb-0">
                                Evaluasi terhadap layanan tugas akhir, skripsi, dan pendampingan penyelesaian studi
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-xl hover-card">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-orange-light p-3 d-inline-flex mb-3">
                                <i class="fas fa-handshake fa-2x text-orange"></i>
                            </div>
                            <h3 class="h5 fw-bold">Kepuasan Mitra</h3>
                            <p class="text-muted mb-0">
                                Evaluasi oleh mitra kerjasama terhadap kualitas hubungan dan implementasi kerjasama
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-xl hover-card">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-orange-light p-3 d-inline-flex mb-3">
                                <i class="fas fa-building fa-2x text-orange"></i>
                            </div>
                            <h3 class="h5 fw-bold">Kepuasan Pengguna Lulusan</h3>
                            <p class="text-muted mb-0">
                                Evaluasi oleh perusahaan/institusi terhadap kualitas alumni FIK UPNVJ
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Tambahan 3 kuesioner baru -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-xl hover-card">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-orange-light p-3 d-inline-flex mb-3">
                                <i class="fas fa-user-tie fa-2x text-orange"></i>
                            </div>
                            <h3 class="h5 fw-bold">Kepuasan Dosen</h3>
                            <p class="text-muted mb-0">
                                Evaluasi kepuasan dosen terhadap layanan dan fasilitas yang disediakan oleh fakultas
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-xl hover-card">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-orange-light p-3 d-inline-flex mb-3">
                                <i class="fas fa-user-cog fa-2x text-orange"></i>
                            </div>
                            <h3 class="h5 fw-bold">Kepuasan Tenaga Kependidikan</h3>
                            <p class="text-muted mb-0">
                                Evaluasi kepuasan tenaga kependidikan terhadap pengelolaan dan lingkungan kerja fakultas
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-xl hover-card">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-orange-light p-3 d-inline-flex mb-3">
                                <i class="fas fa-user-check fa-2x text-orange"></i>
                            </div>
                            <h3 class="h5 fw-bold">Kepuasan Alumni</h3>
                            <p class="text-muted mb-0">
                                Evaluasi kepuasan alumni terhadap kebermanfaatan pendidikan di FIK UPNVJ untuk karir mereka
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="card border-0 bg-orange text-white shadow rounded-xl">
        <div class="card-body p-5 text-center">
            <h2 class="fw-bold mb-3">Bergabunglah dalam Peningkatan Mutu FIK UPNVJ</h2>
            <p class="lead mb-4">
                Partisipasi Anda sangat berarti untuk pengembangan dan peningkatan kualitas layanan fakultas
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="{{ route('public.questionnaire.index') }}" class="btn btn-light text-orange btn-lg px-4 rounded-xl fw-bold">
                    <i class="fas fa-clipboard-list me-2"></i> Isi Kuesioner Sekarang
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4 rounded-xl">
                    <i class="fas fa-sign-in-alt me-2"></i> Login
                </a>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .hover-card {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(255, 107, 53, 0.15) !important;
        }

        .shadow-orange {
            box-shadow: 0 0.15rem 1.75rem rgba(255, 107, 53, 0.15) !important;
        }

        .rounded-top-3 {
            border-top-left-radius: 0.75rem !important;
            border-top-right-radius: 0.75rem !important;
        }

        .rounded-bottom-3 {
            border-bottom-left-radius: 0.75rem !important;
            border-bottom-right-radius: 0.75rem !important;
        }

        /* Styles for clickable questionnaire cards */
        .questionnaire-card-link {
            display: block;
            transition: background-color 0.2s ease;
        }

        .questionnaire-card-link:hover {
            background-color: rgba(255, 255, 255, 0.5);
        }

        /* Hide the details badge by default */
        .view-details-badge {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        /* Show the details badge on hover */
        .questionnaire-card-link:hover .view-details-badge {
            opacity: 1;
        }
    </style>
@endsection

@section('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Konfigurasi warna tema
            const themeColors = {
                primary: '#007bff',
                primaryLight: '#e6f0ff',
                success: '#28a745',
                warning: '#ffc107',
                danger: '#ff1a1a',
                orange: '#ff6b35',
            };

            // Data untuk chart radar
            const radarData = {
                labels: [],
                values: []
            };

            // Mengisi data dari objek PHP ke JavaScript
            @foreach ($dashboardData['questionnaire_types'] ?? [] as $type => $data)
                radarData.labels.push('{{ $type }}');
                radarData.values.push({{ $data['average'] ?? 0 }});
            @endforeach

            // Nama label yang lebih pendek untuk chart
            const chartLabels = {
                'layanan_fakultas': 'Layanan Fakultas',
                'elom': 'ELOM',
                'evaluasi_dosen': 'Evaluasi Dosen',
                'elta': 'ELTA',
                'kepuasan_dosen': 'Dosen',
                'kepuasan_tendik': 'Tendik',
                'kepuasan_alumni': 'Alumni',
                'kepuasan_pengguna_lulusan': 'Pengguna Lulusan',
                'kepuasan_mitra': 'Mitra'
            };

            // Menyiapkan data untuk radar chart
            const labels = radarData.labels.map(label => chartLabels[label] || label);
            const data = radarData.values;

            // Fungsi untuk mendapatkan warna berdasarkan nilai
            function getColorByValue(value) {
                if (value >= 3.5) return themeColors.success;
                if (value >= 3.0) return themeColors.primary;
                if (value >= 2.0) return themeColors.warning;
                return themeColors.danger;
            }

            // Membuat radar chart
            const radarChart = document.getElementById('radar-chart');
            if (radarChart) {
                new Chart(radarChart, {
                    type: 'radar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Nilai Rata-rata',
                            data: data,
                            backgroundColor: 'rgba(255, 107, 53, 0.2)',
                            borderColor: themeColors.orange,
                            borderWidth: 2,
                            pointBackgroundColor: data.map(getColorByValue),
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: data.map(getColorByValue),
                            pointRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                angleLines: {
                                    display: true
                                },
                                beginAtZero: false,
                                min: 0,
                                max: 4,
                                ticks: {
                                    stepSize: 1,
                                    font: {
                                        size: 12
                                    }
                                },
                                pointLabels: {
                                    font: {
                                        size: 12
                                    },
                                    padding: 15
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    padding: 20,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#fff',
                                titleColor: '#333',
                                bodyColor: '#333',
                                borderColor: themeColors.primary,
                                borderWidth: 1,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        const value = context.raw;
                                        let category = 'Kurang';

                                        if (value >= 3.5) category = 'Sangat Baik';
                                        else if (value >= 3.0) category = 'Baik';
                                        else if (value >= 2.0) category = 'Cukup';

                                        return `${context.label}: ${value.toFixed(2)} (${category})`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
