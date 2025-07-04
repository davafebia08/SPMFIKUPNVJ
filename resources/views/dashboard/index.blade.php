@extends('layouts.app')

@section('title', 'Dashboard - SPM FIK UPNVJ')

@section('content')
    <div class="container py-4">
        <!-- Header Utama -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 bg-orange-light p-4 rounded-3">
            <div class="col-md-6 mb-4 mb-md-0">
                <h1 class="h3 fw-bold mb-2 text-orange">Selamat Datang di Sistem Pelayanan Minimal</h1>
                <p class="text-muted mb-3">
                    Fakultas Ilmu Komputer, UPNVJ<br>
                    Evaluasi dan Tingkatkan Kualitas Layanan FIK.
                </p>
                <a href="{{ route('questionnaires.select') }}" class="btn btn-primary px-4">
                    <i class="fas fa-clipboard-list me-2"></i>Isi Kuesioner
                </a>
            </div>
            <div class="col-md-5 text-center">
                <img src="{{ asset('images/logoupn.png') }}" alt="Welcome Image" class="img-fluid rounded border-0"
                    style="max-height: 200px; border: 2px solid #FF6B35;">
            </div>
        </div>

        <!-- Judul Periode -->
        <h2 class="h4 fw-bold mb-3 text-orange">Grafik SPM FIK UPNVJ Periode {{ $activePeriod->name ?? date('Y') . '/' . (date('Y') + 1) }}</h2>

        <!-- Kartu Nilai Total SPM -->
        @php
            // Hitung nilai total SPM (rata-rata dari semua kuesioner)
            $totalSPM = 0;
            $count = 0;
            $questionnaireTypes = [
                'layanan_fakultas',
                'evaluasi_dosen',
                'elom',
                'elta',
                'kepuasan_dosen',
                'kepuasan_tendik',
                'kepuasan_alumni',
                'kepuasan_pengguna_lulusan',
                'kepuasan_mitra',
            ];

            foreach ($questionnaireTypes as $type) {
                if (isset($reports[$type])) {
                    $report = $reports[$type][0] ?? null;
                    if ($report && isset($report->summary_data)) {
                        $summaryData = is_string($report->summary_data) ? json_decode($report->summary_data, true) : $report->summary_data;
                        if (isset($summaryData['average_total'])) {
                            $totalSPM += floatval($summaryData['average_total']);
                            $count++;
                        }
                    }
                }
            }

            $averageSPM = $count > 0 ? $totalSPM / $count : 0;

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
                        <p class="mb-1"><strong>Periode:</strong> {{ $activePeriod->name ?? date('Y') . '/' . (date('Y') + 1) }}</p>
                        <p class="mb-1"><strong>Total Kuesioner:</strong> {{ $count }} dari {{ count($questionnaireTypes) }}</p>
                        <p class="mb-0"><strong>Update Terakhir:</strong> {{ now()->format('d F Y H:i') }}</p>
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
                            <div class="fs-6">≥ 2.0 - &lt; 3.0</div>
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

        <!-- Grafik Utama Radar -->
        <div class="card mb-5 shadow-orange">
            <div class="card-header bg-orange text-white rounded-top-3">
                <h5 class="mb-0">Grafik Radar Semua Kuesioner</h5>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center bg-orange-light"
                style="height: 650px; padding: 20px; border-radius: 0 0 12px 12px;">
                <div style="width: 100%; height: 600px; position: relative;">
                    <canvas id="main-chart"></canvas>
                </div>
            </div>
        </div>

        <!-- Kartu Kuesioner -->
        <h3 class="fw-bold mb-4 text-orange">Detail Nilai Kuesioner</h3>
        <div class="row mb-4">
            @foreach ([
            ['id' => 'layanan-fakultas', 'type' => 'layanan_fakultas', 'title' => 'Kepuasan Layanan Fakultas', 'icon' => 'fas fa-building'],
            ['id' => 'evaluasi-dosen', 'type' => 'evaluasi_dosen', 'title' => 'Evaluasi Dosen oleh Mahasiswa', 'icon' => 'fas fa-chalkboard-teacher'],
            ['id' => 'elom', 'type' => 'elom', 'title' => 'Kepuasan Mahasiswa (ELOM)', 'icon' => 'fas fa-user-graduate'],
            ['id' => 'elta', 'type' => 'elta', 'title' => 'Kepuasan Tugas Akhir (ELTA)', 'icon' => 'fas fa-file-alt'],
            ['id' => 'kepuasan-dosen', 'type' => 'kepuasan_dosen', 'title' => 'Kepuasan Dosen', 'icon' => 'fas fa-user-tie'],
            ['id' => 'kepuasan-tendik', 'type' => 'kepuasan_tendik', 'title' => 'Kepuasan Tenaga Kependidikan', 'icon' => 'fas fa-users'],
            ['id' => 'kepuasan-alumni', 'type' => 'kepuasan_alumni', 'title' => 'Kepuasan Alumni', 'icon' => 'fas fa-user-graduate'],
            ['id' => 'pengguna-lulusan', 'type' => 'kepuasan_pengguna_lulusan', 'title' => 'Kepuasan Pengguna Lulusan', 'icon' => 'fas fa-briefcase'],
            ['id' => 'kepuasan-mitra', 'type' => 'kepuasan_mitra', 'title' => 'Kepuasan Mitra Kerjasama', 'icon' => 'fas fa-handshake'],
        ] as $item)
                <div class="col-md-4 mb-4">
                    <a href="{{ route('questionnaire.details', $item['type']) }}" class="text-decoration-none">
                        <div class="card h-100 shadow-orange">
                            <div class="card-header bg-orange text-white rounded-top-3">
                                <h5 class="mb-0">
                                    <i class="{{ $item['icon'] }} me-2"></i>{{ $item['title'] }}
                                </h5>
                            </div>
                            <div class="card-body bg-orange-light rounded-bottom-3 text-center p-3">
                                <div style="height: 150px; width: 150px; margin: 0 auto;">
                                    <canvas id="chart-{{ $item['id'] }}"></canvas>
                                </div>
                                <div class="mt-3 fw-bold" id="category-{{ $item['id'] }}"></div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Riwayat dan Kuesioner Tersedia -->
        <div class="row mt-4">
            <div class="col-lg-6 mb-4">
                <div class="card h-100 shadow-orange">
                    <div class="card-header bg-orange text-white rounded-top-3">
                        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Kuesioner Tersedia</h5>
                    </div>
                    <div class="card-body bg-orange-light rounded-bottom-3 p-0">
                        @if (isset($availableQuestionnaires) && count($availableQuestionnaires) > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($availableQuestionnaires as $questionnaire)
                                    <a href="{{ route('responses.fill', $questionnaire->id) }}" class="list-group-item list-group-item-action"
                                        style="border-left: 4px solid #FF6B35;">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="card-title mb-1">{{ $questionnaire->title }}</h6>
                                                <p class="text-muted mb-1 small">{{ Str::limit($questionnaire->description, 80) }}</p>
                                            </div>
                                            <small class="text-muted text-nowrap ms-2">
                                                Sampai {{ date('d/m/Y', strtotime($questionnaire->end_date)) }}
                                            </small>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center p-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5>Tidak ada kuesioner tersedia</h5>
                                <p class="text-muted">Saat ini tidak ada kuesioner yang perlu diisi</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-orange text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Riwayat Pengisian</h5>
                        <a href="{{ route('responses.history') }}" class="btn btn-sm btn-light text-orange">
                            <i class="fas fa-history me-1"></i> Lihat Semua
                        </a>
                    </div>
                    <div class="card-body">
                        @if (isset($completedQuestionnaires) && count($completedQuestionnaires) > 0)
                            <div class="list-group">
                                @foreach ($completedQuestionnaires->take(5) as $questionnaire)
                                    <a href="{{ route('responses.history.detail', $questionnaire) }}" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">{{ $questionnaire->title }}</h5>
                                            <small>
                                                @if (isset($questionnaire->pivot) && isset($questionnaire->pivot->submitted_at))
                                                    {{ \Carbon\Carbon::parse($questionnaire->pivot->submitted_at)->format('d/m/Y H:i') }}
                                                @elseif (isset($questionnaire->submitted_at))
                                                    @if (is_string($questionnaire->submitted_at))
                                                        {{ date('d/m/Y H:i', strtotime($questionnaire->submitted_at)) }}
                                                    @else
                                                        {{ $questionnaire->submitted_at->format('d/m/Y H:i') }}
                                                    @endif
                                                @else
                                                    Waktu tidak tersedia
                                                @endif
                                            </small>
                                        </div>
                                        <p class="mb-1">{{ $questionnaire->description }}</p>
                                        <div class="d-flex align-items-center mt-1">
                                            <span class="badge bg-success me-2">Sudah diisi</span>
                                            <small class="text-primary">Klik untuk melihat detail</small>
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            @if ($completedQuestionnaires->count() > 5)
                                <div class="text-center mt-3">
                                    <a href="{{ route('responses.history') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i> Lihat {{ $completedQuestionnaires->count() - 5 }} Lainnya
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-clipboard fa-3x text-muted mb-3"></i>
                                <p class="mb-3">Anda belum pernah mengisi kuesioner.</p>
                                <a href="{{ route('questionnaires.select') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-clipboard-list me-1"></i> Isi Kuesioner Sekarang
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data untuk grafik dari reports
            const reports = @json($reports ?? []);
            const categories = ['Reliability', 'Responsiveness', 'Assurance', 'Empathy', 'Tangible'];

            // Warna tema dari layout
            const themeColors = {
                primary: '#007bff',
                primaryLight: '#FFF3EB',
                primaryDark: '#E55627',
                accent: '#FF9E1B',
                success: '#34a853',
                warning: '#fbbc05',
                danger: '#ea4335',
                gray: '#6c757d',
                orange: '#FF6B35'
            };

            // Fungsi untuk mendapatkan kategori berdasarkan nilai
            function getRatingCategory(value) {
                if (value >= 3.5) return {
                    text: 'Sangat Baik',
                    color: themeColors.success
                };
                if (value >= 3.0) return {
                    text: 'Baik',
                    color: themeColors.primary
                };
                if (value >= 2.0) return {
                    text: 'Cukup',
                    color: themeColors.warning
                };
                return {
                    text: 'Kurang',
                    color: themeColors.danger
                };
            }

            // Fungsi untuk ekstrak data dari reports
            function getReportDataForType(type) {
                if (reports && reports[type] && reports[type].length > 0) {
                    const report = reports[type][0] ?? null;
                    if (report && report.summary_data) {
                        const summaryData = typeof report.summary_data === 'string' ?
                            JSON.parse(report.summary_data) :
                            report.summary_data;

                        return {
                            average: parseFloat(summaryData.average_total) || 3.5,
                            categories: summaryData.category_data || []
                        };
                    }
                }

                // Data default jika tidak ada
                return {
                    average: 3.5,
                    categories: categories.map(cat => ({
                        category: cat,
                        average_rating: 3.5
                    }))
                };
            }

            // Data untuk grafik utama
            const questionnairesData = [{
                    type: 'layanan_fakultas',
                    label: 'Layanan Fakultas'
                },
                {
                    type: 'elom',
                    label: 'ELOM'
                },
                {
                    type: 'evaluasi_dosen',
                    label: 'Evaluasi Dosen'
                },
                {
                    type: 'elta',
                    label: 'ELTA'
                },
                {
                    type: 'kepuasan_dosen',
                    label: 'Kepuasan Dosen'
                },
                {
                    type: 'kepuasan_tendik',
                    label: 'Kepuasan Tendik'
                },
                {
                    type: 'kepuasan_alumni',
                    label: 'Kepuasan Alumni'
                },
                {
                    type: 'kepuasan_pengguna_lulusan',
                    label: 'Pengguna Lulusan'
                },
                {
                    type: 'kepuasan_mitra',
                    label: 'Kepuasan Mitra'
                }
            ];

            const mainChartLabels = questionnairesData.map(q => q.label);
            const mainChartData = questionnairesData.map(q => {
                const data = getReportDataForType(q.type);
                return parseFloat(data.average) || 3.5;
            });

            // Warna untuk radar chart berdasarkan nilai
            const mainChartBorderColors = mainChartData.map(value => getRatingCategory(value).color);

            // Membuat chart utama
            const mainChartCtx = document.getElementById('main-chart');
            if (mainChartCtx) {
                const mainChart = new Chart(mainChartCtx, {
                    type: 'radar',
                    data: {
                        labels: mainChartLabels,
                        datasets: [{
                            label: 'Nilai Rata-rata',
                            data: mainChartData,
                            backgroundColor: 'rgba(255, 107, 53, 0.2)',
                            borderColor: themeColors.orange,
                            borderWidth: 2,
                            pointBackgroundColor: mainChartBorderColors,
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: mainChartBorderColors
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const value = context.raw;
                                        const category = getRatingCategory(value);
                                        return `${context.label}: ${value.toFixed(2)} (${category.text})`;
                                    }
                                }
                            },
                            legend: {
                                position: 'top',
                                labels: {
                                    padding: 20,
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        },
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
                        }
                    }
                });
            }

            // Buat chart donut untuk setiap jenis kuesioner
            function createDonutChart(id, type) {
                const data = getReportDataForType(type);
                const average = parseFloat(data.average) || 3.5;
                const category = getRatingCategory(average);

                const ctx = document.getElementById('chart-' + id);
                if (!ctx) {
                    console.error('Canvas element not found: chart-' + id);
                    return;
                }

                // Update category badge
                const categoryEl = document.getElementById('category-' + id);
                if (categoryEl) {
                    categoryEl.textContent = `${category.text} (${average.toFixed(2)})`;
                    categoryEl.style.color = category.color;
                }

                try {
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Nilai', ''],
                            datasets: [{
                                data: [average, 4 - average],
                                backgroundColor: [category.color, themeColors.primaryLight],
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
                                                return `Nilai: ${average.toFixed(2)} (${category.text})`;
                                            }
                                            return '';
                                        }
                                    }
                                }
                            }
                        }
                    });
                } catch (error) {
                    console.error('Error creating chart for ' + id, error);
                }
            }

            // Buat semua chart donut
            try {
                createDonutChart('layanan-fakultas', 'layanan_fakultas');
                createDonutChart('evaluasi-dosen', 'evaluasi_dosen');
                createDonutChart('elom', 'elom');
                createDonutChart('elta', 'elta');
                createDonutChart('kepuasan-dosen', 'kepuasan_dosen');
                createDonutChart('kepuasan-tendik', 'kepuasan_tendik');
                createDonutChart('kepuasan-alumni', 'kepuasan_alumni');
                createDonutChart('pengguna-lulusan', 'kepuasan_pengguna_lulusan');
                createDonutChart('kepuasan-mitra', 'kepuasan_mitra');
            } catch (error) {
                console.error('Error creating donut charts:', error);
            }
        });
    </script>
@endsection
