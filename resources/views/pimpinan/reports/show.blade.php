@extends('layouts.pimpinan')

@section('title', 'Detail Laporan Kuesioner')

@section('content')
    <div class="container-fluid">
        <!-- Header dan tombol cetak -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Detail Laporan: {{ $questionnaire->title }}</h1>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('pimpinan.reports.print', ['report' => $questionnaire->id, 'orientation' => 'portrait']) }}"
                    class="btn btn-outline-primary btn-sm shadow-sm" target="_blank">
                    <i class="fas fa-print me-1"></i> Cetak Laporan
                </a>
                <a href="{{ route('pimpinan.reports.print', ['report' => $questionnaire->id, 'orientation' => 'landscape']) }}"
                    class="btn btn-outline-danger btn-sm shadow-sm" target="_blank">
                    <i class="fas fa-file-alt me-1"></i> Cetak Data Detail
                </a>
            </div>

        </div>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('pimpinan.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pimpinan.reports.index') }}">Laporan</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $questionnaire->title }}</li>
            </ol>
        </nav>

        <!-- Card Informasi Kuesioner -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Kuesioner</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Judul:</strong></td>
                                <td>{{ $questionnaire->title }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tipe:</strong></td>
                                <td>{{ ucfirst(str_replace('_', ' ', $questionnaire->type)) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Periode:</strong></td>
                                <td>{{ $questionnaire->academicPeriod->name ?? 'Tidak ada periode' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Deskripsi:</strong></td>
                                <td>{{ $questionnaire->description ?? 'Tidak ada deskripsi' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Tanggal Mulai:</strong></td>
                                <td>{{ $questionnaire->start_date ? date('d M Y', strtotime($questionnaire->start_date)) : 'Belum ditentukan' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Selesai:</strong></td>
                                <td>{{ $questionnaire->end_date ? date('d M Y', strtotime($questionnaire->end_date)) : 'Belum ditentukan' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if ($questionnaire->is_active)
                                        Aktif
                                    @else
                                        Tidak Aktif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Dibuat pada:</strong></td>
                                <td>{{ $questionnaire->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Statistik Utama -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Responden</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($responses->groupBy('user_id')) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Skor Rata-rata</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($overallAverage, 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-star fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Jumlah Pertanyaan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $questions->count() }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Penilaian</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @if ($overallAverage >= 3.5)
                                        Sangat Baik
                                    @elseif($overallAverage >= 2.5)
                                        Baik
                                    @elseif($overallAverage >= 1.5)
                                        Cukup
                                    @else
                                        Kurang
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-award fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Rata-rata Per Kategori -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Rata-rata Per Kategori</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Opsi Grafik:</div>
                        <a class="dropdown-item" href="#" id="downloadChart">Download Grafik</a>
                        <a class="dropdown-item" href="#" id="showDataTable">Tampilkan Tabel</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-bar">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="mt-4 text-center">
                    <h5>Tabel Skor Kategori</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Skor Rata-rata</th>
                                    <th>Penilaian</th>
                                </tr>
                            </thead>
                            <!-- Bagian tabel skor kategori -->
                            <tbody>
                                @foreach ($categories as $category)
                                    @php
                                        $catScore = isset($categoryStats[$category->id]) ? $categoryStats[$category->id]['average'] : 0;
                                        $assessment = '';

                                        if ($catScore >= 3.5) {
                                            $assessment = 'Sangat Baik';
                                        } elseif ($catScore >= 2.5) {
                                            $assessment = 'Baik';
                                        } elseif ($catScore >= 1.5) {
                                            $assessment = 'Cukup';
                                        } else {
                                            $assessment = 'Kurang';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ number_format($catScore, 2) }}</td>
                                        <td>
                                            @if ($catScore >= 3.5)
                                                <div
                                                    style="display: inline-block; padding: 4px 8px; background-color: #28a745; color: white; border-radius: 4px; font-weight: bold;">
                                                    {{ $assessment }}</div>
                                            @elseif($catScore >= 2.5)
                                                <div
                                                    style="display: inline-block; padding: 4px 8px; background-color: #007bff; color: white; border-radius: 4px; font-weight: bold;">
                                                    {{ $assessment }}</div>
                                            @elseif($catScore >= 1.5)
                                                <div
                                                    style="display: inline-block; padding: 4px 8px; background-color: #ffc107; color: black; border-radius: 4px; font-weight: bold;">
                                                    {{ $assessment }}</div>
                                            @else
                                                <div
                                                    style="display: inline-block; padding: 4px 8px; background-color: #dc3545; color: white; border-radius: 4px; font-weight: bold;">
                                                    {{ $assessment }}</div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="bg-light font-weight-bold">
                                    <td>Rata-rata Keseluruhan</td>
                                    <td>{{ number_format($overallAverage, 2) }}</td>
                                    <td>
                                        @if ($overallAverage >= 3.5)
                                            <div
                                                style="display: inline-block; padding: 4px 8px; background-color: #28a745; color: white; border-radius: 4px; font-weight: bold;">
                                                {{ $assessment }}</div>
                                        @elseif($overallAverage >= 2.5)
                                            <div
                                                style="display: inline-block; padding: 4px 8px; background-color: #007bff; color: white; border-radius: 4px; font-weight: bold;">
                                                {{ $assessment }}</div>
                                        @elseif($overallAverage >= 1.5)
                                            <div
                                                style="display: inline-block; padding: 4px 8px; background-color: #ffc107; color: black; border-radius: 4px; font-weight: bold;">
                                                {{ $assessment }}</div>
                                        @else
                                            <div
                                                style="display: inline-block; padding: 4px 8px; background-color: #dc3545; color: white; border-radius: 4px; font-weight: bold;">
                                                {{ $assessment }}</div>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nav tabs untuk detail per kategori -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Detail Per Kategori</h6>
            </div>
            <div class="card-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" id="categoryTabs" role="tablist">
                    @foreach ($categories as $index => $category)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $index === 0 ? 'active' : '' }}" id="cat-{{ $category->id }}-tab" data-toggle="tab"
                                href="#cat-{{ $category->id }}" role="tab" aria-controls="cat-{{ $category->id }}"
                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mt-3" id="categoryTabContent">
                    @foreach ($categories as $index => $category)
                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="cat-{{ $category->id }}" role="tabpanel"
                            aria-labelledby="cat-{{ $category->id }}-tab">
                            @if (isset($categoryStats[$category->id]))
                                <div class="p-3">
                                    <h5>Rata-rata Kategori: {{ number_format($categoryStats[$category->id]['average'], 2) }}</h5>
                                    <div class="table-responsive mt-3">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Pertanyaan</th>
                                                    <th>Sangat Baik (4)</th>
                                                    <th>Baik (3)</th>
                                                    <th>Cukup (2)</th>
                                                    <th>Kurang (1)</th>
                                                    <th>Rata-rata</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $categoryQuestions = $questions->where('category_id', $category->id)->sortBy('order');
                                                @endphp
                                                @foreach ($categoryQuestions as $questionIndex => $question)
                                                    @php
                                                        $stats = $questionStats[$question->id]['stats'] ?? [1 => 0, 2 => 0, 3 => 0, 4 => 0];
                                                        $average = $questionStats[$question->id]['average'] ?? 0;
                                                        $total = $questionStats[$question->id]['total'] ?? 0;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $questionIndex + 1 }}</td>
                                                        <td>{{ $question->question }}</td>
                                                        <td>{{ $stats[4] }} ({{ $total > 0 ? number_format(($stats[4] / $total) * 100, 1) : 0 }}%)
                                                        </td>
                                                        <td>{{ $stats[3] }} ({{ $total > 0 ? number_format(($stats[3] / $total) * 100, 1) : 0 }}%)
                                                        </td>
                                                        <td>{{ $stats[2] }} ({{ $total > 0 ? number_format(($stats[2] / $total) * 100, 1) : 0 }}%)
                                                        </td>
                                                        <td>{{ $stats[1] }} ({{ $total > 0 ? number_format(($stats[1] / $total) * 100, 1) : 0 }}%)
                                                        </td>
                                                        <td>{{ number_format($average, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="p-3">
                                    <p>Tidak ada data untuk kategori ini.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Detail Responden Summary -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Ringkasan Responden</h6>
                <a href="{{ route('pimpinan.results.respondents', $questionnaire->id) }}" class="btn btn-sm btn-primary">
                    Lihat Semua Responden
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center" id="respondentTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Responden</th>
                                <th>Kategori</th>
                                <th>Program Studi</th>
                                <th>Tanggal Pengisian</th>
                                <th>Rata-rata Nilai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($responses->groupBy('user_id')->take(10) as $userId => $userResponses)
                                @php
                                    // Mendapatkan data responden
                                    $respUser = App\Models\User::find($userId);
                                    $userName = $respUser ? $respUser->name : 'Anonim';

                                    // Format role untuk tampilan yang lebih baik
                                    $roleMapping = [
                                        'mahasiswa' => 'Mahasiswa',
                                        'dosen' => 'Dosen',
                                        'tendik' => 'Tenaga Kependidikan',
                                        'alumni' => 'Alumni',
                                        'pengguna_lulusan' => 'Pengguna Lulusan',
                                        'mitra' => 'Mitra Kerjasama',
                                        'admin' => 'Administrator',
                                        'pimpinan' => 'Pimpinan',
                                    ];

                                    $userRole = isset($respUser) ? $roleMapping[$respUser->role] ?? ucfirst($respUser->role) : '-';
                                    $userProdi = isset($respUser) ? $respUser->program_studi ?? '-' : '-';

                                    $timestamp = $userResponses->first()->created_at ?? null;
                                    $respNo = $loop->index + 1;
                                    $userRatings = $userResponses->pluck('rating');
                                    $userAverage = $userRatings->avg();
                                @endphp
                                <tr>
                                    <td>{{ $respNo }}</td>
                                    <td class="text-start">{{ $userName }}</td>
                                    <td class="text-start">{{ $userRole }}</td>
                                    <td class="text-start">{{ $userProdi }}</td>
                                    <td>{{ $timestamp ? $timestamp->format('d M Y H:i') : '-' }}</td>
                                    <td>{{ number_format($userAverage, 2) }}</td>
                                    <td>
                                        <a href="{{ route('pimpinan.results.respondent-detail', [$questionnaire->id, $userId]) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Form untuk mengisi analisa, simpulan, dan RTL -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Konten Laporan</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.reports.update-content', $questionnaire->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="analysis_content"><strong>3.2. Analisa & Pembahasan</strong></label>
                        <textarea class="form-control" id="analysis_content" name="analysis_content" rows="6">{{ $report->analysis_content ?? '' }}</textarea>
                        <small class="form-text text-muted">Masukkan analisa dan pembahasan hasil kuesioner di sini.</small>
                    </div>

                    <div class="form-group">
                        <label for="conclusion_content"><strong>4.1. Simpulan</strong></label>
                        <textarea class="form-control" id="conclusion_content" name="conclusion_content" rows="4">{{ $report->conclusion_content ?? '' }}</textarea>
                        <small class="form-text text-muted">Masukkan simpulan dari hasil kuesioner di sini.</small>
                    </div>

                    <div class="form-group">
                        <label for="followup_content"><strong>4.2. Rencana Tindak Lanjut (RTL)</strong></label>
                        <textarea class="form-control" id="followup_content" name="followup_content" rows="4">{{ $report->followup_content ?? '' }}</textarea>
                        <small class="form-text text-muted">Masukkan rencana tindak lanjut di sini.</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Konten</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Grafik Kategori
        const categoryChart = document.getElementById('categoryChart');
        const catChart = new Chart(categoryChart, {
            type: 'bar',
            data: {
                labels: [
                    @foreach ($categories as $category)
                        "{{ $category->name }}",
                    @endforeach
                ],
                datasets: [{
                    label: 'Rata-rata Nilai',
                    data: [
                        @foreach ($categories as $category)
                            {{ isset($categoryStats[$category->id]) ? $categoryStats[$category->id]['average'] : 0 }},
                        @endforeach
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 4,
                        ticks: {
                            stepSize: 0.5
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
                                return `Rata-rata: ${context.raw.toFixed(2)}`;
                            }
                        }
                    }
                }
            }
        });

        // Download Chart Button
        document.getElementById('downloadChart').addEventListener('click', function() {
            const link = document.createElement('a');
            link.download = 'chart-{{ Str::slug($questionnaire->title) }}.png';
            link.href = categoryChart.toDataURL('image/png');
            link.click();
        });

        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Pastikan jQuery tersedia
            if (typeof $ !== 'undefined') {
                // Inisialisasi tab dengan jQuery
                $('#categoryTabs a').on('click', function(e) {
                    e.preventDefault();
                    $(this).tab('show');
                });

                // Memastikan tab pertama aktif saat halaman dimuat
                $('#categoryTabs a:first').tab('show');

                // DataTable Initialization jika tersedia
                if ($.fn.DataTable) {
                    $('#respondentTable').DataTable({
                        paging: true,
                        searching: true,
                        ordering: true,
                        lengthMenu: [5, 10, 25, 50],
                        pageLength: 5
                    });
                }
            } else {
                // Fallback ke pure JavaScript jika jQuery tidak tersedia
                var tabLinks = document.querySelectorAll('#categoryTabs a');

                tabLinks.forEach(function(link) {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();

                        // Hide all tabs
                        document.querySelectorAll('.tab-pane').forEach(function(tab) {
                            tab.classList.remove('show', 'active');
                        });

                        // Remove active class from all tab links
                        tabLinks.forEach(function(tabLink) {
                            tabLink.classList.remove('active');
                        });

                        // Add active class to current tab link
                        this.classList.add('active');

                        // Show current tab
                        var tabId = this.getAttribute('href');
                        document.querySelector(tabId).classList.add('show', 'active');
                    });
                });

                // Activate first tab by default
                if (tabLinks.length > 0) {
                    var firstTab = tabLinks[0];
                    firstTab.classList.add('active');
                    var firstTabId = firstTab.getAttribute('href');
                    document.querySelector(firstTabId).classList.add('show', 'active');
                }
            }
        });
    </script>
@endpush
