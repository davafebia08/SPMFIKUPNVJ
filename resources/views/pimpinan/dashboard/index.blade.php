@extends('layouts.pimpinan')

@section('title', 'Dashboard Pimpinan - SPM FIK UPNVJ')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Dashboard Pimpinan</h1>
                <p class="mb-0 text-muted">Ringkasan Evaluasi SPM FIK UPNVJ</p>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filter Dashboard</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('pimpinan.dashboard') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="academic_period_id" class="form-label">Periode Akademik</label>
                            <select name="academic_period_id" id="academic_period_id" class="form-select">
                                <option value="all" {{ $selectedPeriodId === 'all' ? 'selected' : '' }}>
                                    Semua Periode
                                </option>
                                @foreach ($academicPeriods as $period)
                                    <option value="{{ $period->id }}" {{ $selectedPeriodId == $period->id ? 'selected' : '' }}>
                                        {{ $period->name }} ({{ $period->semester }} {{ $period->year }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="program_studi" class="form-label">Program Studi</label>
                            <select name="program_studi" id="program_studi" class="form-select">
                                <option value="">Semua Program Studi</option>
                                @foreach ($programStudiOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $selectedProdi == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
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

        <!-- Tambahkan alert info untuk periode dan program studi yang dipilih -->
        @if ($selectedPeriodId === 'all' || $selectedProdi)
            <div class="alert alert-info mb-4">
                <i class="fas fa-filter me-2"></i>
                @if ($selectedPeriodId === 'all' && $selectedProdi)
                    Menampilkan data untuk <strong>Semua Periode</strong> dan Program Studi: <strong>{{ $selectedProdi }}</strong>
                @elseif ($selectedPeriodId === 'all')
                    Menampilkan data untuk <strong>Semua Periode</strong>
                @elseif ($selectedProdi)
                    Menampilkan data untuk Program Studi: <strong>{{ $selectedProdi }}</strong>
                @endif
            </div>
        @endif
        <br>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Kuesioner
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $dashboardData['total_questionnaires'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Jumlah Responden
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $dashboardData['total_respondents'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Nilai SPM
                                    @if ($selectedPeriodId === 'all')
                                        <small class="text-muted">(Rata-rata Semua Periode)</small>
                                    @endif
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($dashboardData['average_spm'], 2) }}
                                    @php
                                        $avgSpm = $dashboardData['average_spm'];
                                        $spmCategory = '';
                                        $spmClass = '';

                                        if ($avgSpm >= 3.5) {
                                            $spmCategory = 'Sangat Baik';
                                            $spmClass = 'success';
                                        } elseif ($avgSpm >= 3.0) {
                                            $spmCategory = 'Baik';
                                            $spmClass = 'primary';
                                        } elseif ($avgSpm >= 2.0) {
                                            $spmCategory = 'Cukup';
                                            $spmClass = 'warning';
                                        } else {
                                            $spmCategory = 'Kurang';
                                            $spmClass = 'danger';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $spmClass }}">{{ $spmCategory }}</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Tren Nilai SPM</h6>
                        <small class="text-muted">5 Periode Terakhir</small>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="spmTrendChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Evaluations -->
        <div class="row mb-4">
            <div class="col-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Evaluasi Berdasarkan Kategori
                            @if ($selectedPeriodId === 'all')
                                <small class="text-muted">(Semua Periode)</small>
                            @endif
                        </h6>
                    </div>
                    <div class="card-body">
                        @if (count($dashboardData['category_averages']) > 0)
                            <div class="row">
                                @foreach ($dashboardData['category_averages'] as $category => $data)
                                    @php
                                        $colorClass = 'danger';
                                        if ($data['average'] >= 3.5) {
                                            $colorClass = 'success';
                                        } elseif ($data['average'] >= 3.0) {
                                            $colorClass = 'primary';
                                        } elseif ($data['average'] >= 2.0) {
                                            $colorClass = 'warning';
                                        }
                                    @endphp
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="card border-left-{{ $colorClass }} shadow h-100">
                                            <div class="card-body">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="text-xs font-weight-bold text-{{ $colorClass }} text-uppercase mb-1">
                                                            {{ $category }}
                                                        </div>
                                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                            {{ number_format($data['average'], 2) }}
                                                            <small class="text-muted">({{ $data['category'] }})</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada data kategori yang tersedia untuk filter yang dipilih.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Questionnaire Types Performance -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Performa Jenis Kuesioner
                            @if ($selectedPeriodId === 'all')
                                <small class="text-muted">(Rata-rata Semua Periode)</small>
                            @endif
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $questionnaire_types = [
                                    'layanan_fakultas' => 'Kepuasan Layanan Fakultas',
                                    'elom' => 'Layanan oleh Mahasiswa',
                                    'evaluasi_dosen' => 'Kepuasan Dosen oleh Mahasiswa',
                                    'elta' => 'Layanan Tugas Akhir',
                                    'kepuasan_dosen' => 'Kepuasan Dosen',
                                    'kepuasan_tendik' => 'Kepuasan Tenaga Kependidikan',
                                    'kepuasan_alumni' => 'Kepuasan Alumni',
                                    'kepuasan_pengguna_lulusan' => 'Kepuasan Pengguna Lulusan',
                                    'kepuasan_mitra' => 'Kepuasan Mitra Kerja Sama',
                                ];
                            @endphp

                            @foreach ($questionnaire_types as $type => $title)
                                @php
                                    $data = $dashboardData['questionnaire_types'][$type] ?? null;
                                    $average = $data ? $data['average'] : 0;
                                    $category = $data ? $data['category'] : 'Belum ada data';

                                    $colorClass = 'secondary';
                                    if ($data) {
                                        if ($average >= 3.5) {
                                            $colorClass = 'success';
                                        } elseif ($average >= 3.0) {
                                            $colorClass = 'primary';
                                        } elseif ($average >= 2.0) {
                                            $colorClass = 'warning';
                                        } else {
                                            $colorClass = 'danger';
                                        }
                                    }
                                @endphp

                                <div class="col-xl-4 col-md-6 mb-4">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-header bg-{{ $colorClass }} text-white py-2">
                                            <h6 class="m-0 font-weight-bold">{{ $title }}</h6>
                                        </div>
                                        <div class="card-body text-center p-4">
                                            @if ($data)
                                                <div class="display-4 font-weight-bold mb-2">{{ number_format($average, 2) }}</div>
                                                <div class="badge bg-{{ $colorClass }} px-3 py-2 mb-3">{{ $category }}</div>
                                                @if ($selectedPeriodId === 'all' && isset($data['count']) && $data['count'] > 1)
                                                    <small class="text-muted d-block mt-2">
                                                        <i class="fas fa-calculator me-1"></i>
                                                        Rata-rata dari {{ $data['count'] }} periode
                                                    </small>
                                                @endif
                                            @else
                                                <div class="text-muted py-4">
                                                    <i class="fas fa-info-circle mb-2"></i><br>
                                                    Belum ada data
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // SPM Trend Chart
        const trendCtx = document.getElementById('spmTrendChart').getContext('2d');
        const trendData = @json($dashboardData['period_trends']);

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendData.map(item => item.period),
                datasets: [{
                    label: 'Nilai SPM',
                    data: trendData.map(item => item.average),
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 0,
                        max: 4,
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7,
                            padding: 10
                        }
                    },
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                let category = '';

                                if (value >= 3.5) category = 'Sangat Baik';
                                else if (value >= 3.0) category = 'Baik';
                                else if (value >= 2.0) category = 'Cukup';
                                else category = 'Kurang';

                                return `Nilai SPM: ${value.toFixed(2)} (${category})`;
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
