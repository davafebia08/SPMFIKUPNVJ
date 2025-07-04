@extends('layouts.app')

@section('title', $titles[$type] ?? 'Detail Kuesioner')

@section('content')
    <div class="container py-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h2 fw-bold text-orange">{{ $titles[$type] ?? 'Detail Kuesioner' }}</h1>
                        <p class="text-muted">{{ $descriptions[$type] ?? 'Detail kuesioner' }}</p>
                    </div>
                    <a href="{{ url('/') }}" class="btn btn-outline-danger">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda
                    </a>
                </div>

                <!-- Informasi Kuesioner -->
                <div class="card border-0 shadow-orange rounded-xl mb-4">
                    <div class="card-header bg-orange text-white py-3">
                        <h5 class="mb-0">Informasi Kuesioner</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Periode:</strong> {{ $activePeriod->name ?? '-' }}</p>
                                <p><strong>Jenis Kuesioner:</strong> {{ $titles[$type] ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Tujuan:</strong> {{ $tujuan }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keterangan Kategori Nilai -->
                <div class="card border-0 shadow-orange rounded-xl mb-4">
                    <div class="card-header bg-orange text-white py-3">
                        <h5 class="mb-0">Keterangan Kategori Nilai</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 col-6 mb-2">
                                <div class="p-3 bg-danger text-white rounded-xl">
                                    <strong>Kurang</strong>
                                    <p class="mb-0">&lt; 2.0</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-2">
                                <div class="p-3 bg-warning text-white rounded-xl">
                                    <strong>Cukup</strong>
                                    <p class="mb-0">≥ 2.0 - &lt; 3.0</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-2">
                                <div class="p-3 bg-primary text-white rounded-xl">
                                    <strong>Baik</strong>
                                    <p class="mb-0">≥ 3.0 - &lt; 3.5</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-2">
                                <div class="p-3 bg-success text-white rounded-xl">
                                    <strong>Sangat Baik</strong>
                                    <p class="mb-0">≥ 3.5</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grafik Utama -->
                <div class="card border-0 shadow-orange rounded-xl mb-4">
                    <div class="card-header bg-orange text-white py-3">
                        <h5 class="mb-0">Nilai Rata-rata per Aspek</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 350px; width: 100%;">
                            <canvas id="main-chart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Aspek-Aspek Kuesioner -->
                <div class="row g-4">
                    @foreach ($categories as $category)
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-orange rounded-xl">
                                <div class="card-header bg-orange text-white py-3">
                                    <h5 class="mb-0">Aspek {{ $category->name }}</h5>
                                </div>
                                <div class="card-body d-flex flex-column text-center">
                                    <!-- Container grafik dengan tinggi tetap -->
                                    <div style="height: 200px; width: 200px; margin: 0 auto; position: relative;">
                                        <canvas id="chart-{{ Str::slug($category->name) }}"></canvas>
                                        <div class="chart-value"
                                            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
                                                font-size: 24px; font-weight: bold;">
                                            {{ number_format($categoryAverages[$category->name] ?? 0, 2) }}
                                        </div>
                                    </div>
                                    <div class="category-info mt-2 mb-2" id="category-info-{{ Str::slug($category->name) }}"></div>

                                    <!-- Pemisah yang jelas -->
                                    <hr class="w-75 mx-auto my-2">

                                    <!-- Deskripsi dalam div terpisah -->
                                    <div class="mt-auto">
                                        <p class="text-muted small">{{ $category->description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryAverages = @json($categoryAverages ?? []);
            const categories = @json($categories->pluck('name') ?? []);
            const chartColors = {
                primary: '#007bff', // Orange primary
                secondary: '#FF9E1B', // Orange accent
                tertiary: '#FFF3EB', // Orange light
                success: '#28a745', // Green
                warning: '#ffc107', // Yellow
                danger: '#dc3545', // Red
                orange: '#FF6B35', // Orange
            };

            // Fungsi untuk mendapatkan kategori berdasarkan nilai
            function getRatingCategory(value) {
                if (value >= 3.5) return 'Sangat Baik';
                if (value >= 3.0) return 'Baik';
                if (value >= 2.0) return 'Cukup';
                return 'Kurang';
            }

            // Fungsi untuk mendapatkan warna berdasarkan kategori
            function getCategoryColor(value) {
                if (value >= 3.5) return chartColors.success;
                if (value >= 3.0) return chartColors.primary;
                if (value >= 2.0) return chartColors.warning;
                return chartColors.danger;
            }

            // Grafik utama yang menampilkan semua kategori
            const mainChartCtx = document.getElementById('main-chart');
            if (mainChartCtx) {
                const categoryValues = categories.map(cat => parseFloat(categoryAverages[cat] || 0));
                const categoryColors = categoryValues.map(val => getCategoryColor(val));

                new Chart(mainChartCtx, {
                    type: 'bar',
                    data: {
                        labels: categories,
                        datasets: [{
                            label: 'Nilai Rata-rata',
                            data: categoryValues,
                            backgroundColor: categoryColors,
                            borderColor: categoryColors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                min: 0,
                                max: 4,
                                ticks: {
                                    stepSize: 1,
                                    callback: function(value) {
                                        if (value === 1) return 'Kurang (1)';
                                        if (value === 2) return 'Cukup (2)';
                                        if (value === 3) return 'Baik (3)';
                                        if (value === 4) return 'Sangat Baik (4)';
                                        return value;
                                    }
                                }
                            },
                            x: {
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const value = context.raw;
                                        const category = getRatingCategory(value);
                                        return `Nilai: ${value.toFixed(2)} (${category})`;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Grafik untuk masing-masing kategori
            categories.forEach(category => {
                const chartId = 'chart-' + category.toLowerCase().replace(/\s+/g, '-');
                const ctx = document.getElementById(chartId);
                if (!ctx) return;

                const value = parseFloat(categoryAverages[category] || 0);
                const categoryText = getRatingCategory(value);
                const color = getCategoryColor(value);

                // Tampilkan teks kategori di bawah grafik
                const infoId = 'category-info-' + category.toLowerCase().replace(/\s+/g, '-');
                const infoEl = document.getElementById(infoId);
                if (infoEl) {
                    infoEl.textContent = categoryText;
                    infoEl.style.color = color;
                    infoEl.style.fontWeight = 'bold';
                }

                // Update warna nilai di tengah donut chart
                const chartValue = document.querySelector(`#chart-${category.toLowerCase().replace(/\s+/g, '-')}`)
                    .closest('div')
                    .querySelector('.chart-value');
                if (chartValue) {
                    chartValue.style.color = color;
                }

                // Buat donut chart
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Nilai', ''],
                        datasets: [{
                            data: [value, 4 - value],
                            backgroundColor: [color, '#f0f0f0'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        if (context.dataIndex === 0) {
                                            return `Nilai: ${value.toFixed(2)} (${categoryText})`;
                                        }
                                        return '';
                                    }
                                }
                            }
                        }
                    }
                });
            });
        });
    </script>
@endsection
