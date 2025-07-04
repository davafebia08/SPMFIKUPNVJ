<!-- resources/views/admin/results/show.blade.php -->
@extends('layouts.admin')

@section('title', $questionnaire->title . ' - Detail Hasil')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Detail Hasil Kuesioner</h1>
                <p class="mb-0 text-muted">{{ $questionnaire->title }}</p>
            </div>

            <div class="mt-3 mt-md-0">
                <a href="{{ route('admin.results.respondents', ['questionnaire' => $questionnaire, 'program_studi' => $selectedProdi]) }}"
                    class="btn btn-info">
                    <i class="fas fa-users me-1"></i> Lihat Responden
                </a>
                <a href="{{ route('admin.results.index') }}" class="btn btn-outline-primary ms-2">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Filter Card -->
        @if (!in_array($questionnaire->type, ['kepuasan_pengguna_lulusan', 'kepuasan_mitra']))
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Filter Berdasarkan Program Studi</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.results.show', $questionnaire) }}" method="GET" id="filterForm">
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="program_studi" class="form-label">Program Studi</label>
                                <select class="form-select" id="program_studi" name="program_studi"
                                    onchange="document.getElementById('filterForm').submit()">
                                    <option value="">Semua Program Studi</option>
                                    @foreach ($programStudiOptions as $value => $label)
                                        <option value="{{ $value }}" {{ $selectedProdi == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Overview Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Ringkasan Hasil Evaluasi</h6>
                @if ($selectedProdi)
                    <p class="mt-1 mb-0 text-muted">Menampilkan hasil untuk Program Studi: {{ $selectedProdi }}</p>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Nilai Rata-rata
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ number_format($overallAverage, 2) }}
                                            @php
                                                $categoryText = 'Kurang';
                                                $categoryClass = 'danger';

                                                if ($overallAverage >= 3.5) {
                                                    $categoryText = 'Sangat Baik';
                                                    $categoryClass = 'success';
                                                } elseif ($overallAverage >= 3.0) {
                                                    $categoryText = 'Baik';
                                                    $categoryClass = 'primary';
                                                } elseif ($overallAverage >= 2.0) {
                                                    $categoryText = 'Cukup';
                                                    $categoryClass = 'warning';
                                                } else {
                                                    $categoryText = 'Kurang';
                                                    $categoryClass = 'danger';
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $categoryClass }}">{{ $categoryText }}</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-star fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Jumlah Responden
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $respondentCount }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Periode
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $questionnaire->academicPeriod->name }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Rentang Tanggal
                                        </div>
                                        <div class="small font-weight-bold text-gray-800">
                                            {{ date('d/m/Y', strtotime($questionnaire->start_date)) }} -
                                            {{ date('d/m/Y', strtotime($questionnaire->end_date)) }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Rating Distribution Chart -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Distribusi Penilaian</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height:350px;">
                            <canvas id="ratingDistributionChart"></canvas>
                        </div>
                        <!-- Data Table -->
                        <div class="mt-3">
                            <div class="row text-center">
                                @php
                                    $total = collect($ratingDistribution)->sum('count');
                                @endphp
                                @foreach ($ratingDistribution as $rating => $data)
                                    <div class="col-3">
                                        <div class="p-2 border rounded">
                                            @php
                                                $colors = [
                                                    1 => ['bg' => 'danger', 'text' => 'Kurang'],
                                                    2 => ['bg' => 'warning', 'text' => 'Cukup'],
                                                    3 => ['bg' => 'primary', 'text' => 'Baik'],
                                                    4 => ['bg' => 'success', 'text' => 'Sangat Baik'],
                                                ];
                                                $color = $colors[$rating];
                                                $percentage = $total > 0 ? round(($data['count'] / $total) * 100, 1) : 0;
                                            @endphp
                                            <div class="badge bg-{{ $color['bg'] }} mb-1">{{ $color['text'] }}</div>
                                            <div class="h6 mb-0">{{ $data['count'] }}</div>
                                            <small class="text-muted">({{ $percentage }}%)</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Ratings Chart -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Nilai Per Kategori</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height:350px;">
                            <canvas id="categoryRatingsChart"></canvas>
                        </div>
                        <!-- Data Table -->
                        <div class="mt-3">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr class="table-light">
                                            <th>Kategori</th>
                                            <th class="text-center">Rata-rata</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($categoryRatings as $category => $data)
                                            @php
                                                $average = $data['average'];
                                                $badgeClass = 'danger';
                                                $badgeText = 'Kurang';

                                                if ($average >= 3.5) {
                                                    $badgeClass = 'success';
                                                    $badgeText = 'Sangat Baik';
                                                } elseif ($average >= 3.0) {
                                                    $badgeClass = 'primary';
                                                    $badgeText = 'Baik';
                                                } elseif ($average >= 2.0) {
                                                    $badgeClass = 'warning';
                                                    $badgeText = 'Cukup';
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $category }}</td>
                                                <td class="text-center font-weight-bold">{{ number_format($average, 2) }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ $badgeClass }}">{{ $badgeText }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question Ratings Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Detail Penilaian Per Pertanyaan</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="questionRatingsTable">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Pertanyaan</th>
                                <th>Nilai Rata-rata</th>
                                <th>Kategori</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($questionRatings as $questionId => $data)
                                @php
                                    $question = $data['question'];
                                    $average = $data['average'];
                                    $categoryName = $question->category->name ?? 'Tidak Terkategori';

                                    $valueClass = 'danger';
                                    $valueText = 'Kurang';

                                    if ($average >= 3.5) {
                                        $valueClass = 'success';
                                        $valueText = 'Sangat Baik';
                                    } elseif ($average >= 3.0) {
                                        $valueClass = 'primary';
                                        $valueText = 'Baik';
                                    } elseif ($average >= 2.0) {
                                        $valueClass = 'warning';
                                        $valueText = 'Cukup';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $categoryName }}</td>
                                    <td>{{ $question->question }}</td>
                                    <td class="text-center font-weight-bold">{{ number_format($average, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $valueClass }}">{{ $valueText }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        // Register the plugin globally
        Chart.register(ChartDataLabels);

        document.addEventListener('DOMContentLoaded', function() {
            // Rating Distribution Chart
            const distributionCtx = document.getElementById('ratingDistributionChart').getContext('2d');
            const distributionData = @json($ratingDistribution);
            const totalResponses = Object.values(distributionData).reduce((sum, item) => sum + item.count, 0);

            new Chart(distributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Kurang (1)', 'Cukup (2)', 'Baik (3)', 'Sangat Baik (4)'],
                    datasets: [{
                        data: [
                            distributionData[1].count,
                            distributionData[2].count,
                            distributionData[3].count,
                            distributionData[4].count
                        ],
                        backgroundColor: [
                            '#DC3545',
                            '#FFC107',
                            '#0D6EFD',
                            '#28A745'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        datalabels: {
                            color: '#fff',
                            font: {
                                weight: 'bold',
                                size: 12
                            },
                            formatter: (value, context) => {
                                if (value === 0) return '';
                                const percentage = totalResponses > 0 ? Math.round((value / totalResponses) * 100) : 0;
                                return `${value}\n(${percentage}%)`;
                            },
                            textAlign: 'center'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw;
                                    const percentage = totalResponses > 0 ? Math.round((value / totalResponses) * 100) : 0;
                                    return `${label}: ${value} responden (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Category Ratings Chart
            const categoryCtx = document.getElementById('categoryRatingsChart').getContext('2d');
            const categoryData = @json($categoryRatings);

            const categories = [];
            const categoryValues = [];
            const categoryColors = [];

            Object.entries(categoryData).forEach(([category, data]) => {
                categories.push(category);
                categoryValues.push(data.average);

                let color = '#DC3545';
                if (data.average >= 3.5) color = '#28A745';
                else if (data.average >= 3.0) color = '#0D6EFD';
                else if (data.average >= 2.0) color = '#FFC107';

                categoryColors.push(color);
            });

            new Chart(categoryCtx, {
                type: 'bar',
                data: {
                    labels: categories,
                    datasets: [{
                        label: 'Nilai Rata-rata',
                        data: categoryValues,
                        backgroundColor: categoryColors,
                        borderWidth: 1,
                        borderColor: categoryColors.map(color => color)
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            color: '#333',
                            font: {
                                weight: 'bold',
                                size: 11
                            },
                            formatter: (value) => {
                                return value.toFixed(2);
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Rata-rata: ${context.raw.toFixed(2)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 4,
                            ticks: {
                                stepSize: 0.5
                            },
                            grid: {
                                borderDash: [5, 5]
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0
                            }
                        }
                    }
                }
            });

            // Initialize DataTable for questions
            $('#questionRatingsTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                },
                "pageLength": 25,
                "order": [
                    [2, "desc"]
                ] // Sort by rata-rata descending
            });
        });
    </script>
@endsection
