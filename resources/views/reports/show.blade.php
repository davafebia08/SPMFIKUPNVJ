@extends('layouts.app')

@section('title', 'Laporan SPM')

@section('content')
    <div class="container py-4">
        <div class="text-center mb-4">
            <h3>LAPORAN HASIL SURVEY PELAYANAN MINIMAL (SPM)</h3>
            <h4>{{ $report->questionnaire->title }}</h4>
            <h5>{{ $report->academicPeriod->name }}</h5>
            <hr>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Rata-rata SPM</h5>
                        <h1 class="display-4">{{ number_format($summaryData['average_total'], 2) }}</h1>
                        <p>Skala 1-4</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Responden</h5>
                        <h1 class="display-4">{{ $summaryData['total_respondents'] }}</h1>
                        <p>Orang</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Kategori</h5>
                        <h1 class="display-4">5</h1>
                        <p>Aspek Penilaian</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Nilai Per Kategori
                    </div>
                    <div class="card-body">
                        <canvas id="categoryChart" height="200"></canvas>
                    </div>
                    <div class="card-footer">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Kategori</th>
                                        <th>Nilai Rata-rata</th>
                                        <th>Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($summaryData['category_data'] as $category)
                                        <tr>
                                            <td>{{ $category->category }}</td>
                                            <td>{{ number_format($category->average_rating, 2) }}</td>
                                            <td>{{ number_format(($category->average_rating / 4) * 100, 2) }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Profil Kategori
                    </div>
                    <div class="card-body">
                        <canvas id="radarChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Distribusi Rating
                    </div>
                    <div class="card-body">
                        <canvas id="ratingChart" height="200"></canvas>
                    </div>
                    <div class="card-footer">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Rating</th>
                                        <th>Jumlah</th>
                                        <th>Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalRatings = array_sum(array_values($summaryData['rating_distribution']));
                                    @endphp
                                    @foreach ($summaryData['rating_distribution'] as $rating => $count)
                                        <tr>
                                            <td>{{ $rating }}</td>
                                            <td>{{ $count }}</td>
                                            <td>{{ number_format(($count / $totalRatings) * 100, 2) }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Komposisi Responden
                    </div>
                    <div class="card-body">
                        <canvas id="respondentChart" height="200"></canvas>
                    </div>
                    <div class="card-footer">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Kategori</th>
                                        <th>Jumlah</th>
                                        <th>Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalRespondentsByRole = array_sum(array_values($summaryData['respondents_by_role']));
                                    @endphp
                                    @foreach ($summaryData['respondents_by_role'] as $role => $count)
                                        <tr>
                                            <td>{{ ucfirst($role) }}</td>
                                            <td>{{ $count }}</td>
                                            <td>{{ number_format(($count / $totalRespondentsByRole) * 100, 2) }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                Detail Nilai Per Pertanyaan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="50%">Pertanyaan</th>
                                <th width="15%">Kategori</th>
                                <th width="15%">Rata-rata</th>
                                <th width="15%">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($summaryData['question_data'] as $index => $question)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $question->question }}</td>
                                    <td>{{ $question->category }}</td>
                                    <td>{{ number_format($question->average_rating, 2) }}</td>
                                    <td>{{ number_format(($question->average_rating / 4) * 100, 2) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if ($report->notes)
            <div class="card mb-4">
                <div class="card-header">
                    Catatan
                </div>
                <div class="card-body">
                    <p>{{ $report->notes }}</p>
                </div>
            </div>
        @endif

        <div class="d-flex justify-content-between mb-4">
            <div>
                <p><strong>Dibuat oleh:</strong> {{ $report->generator->name }}</p>
                <p><strong>Tanggal:</strong> {{ $report->generated_at->format('d F Y H:i') }}</p>
            </div>
            <div class="text-end">
                <a href="{{ route('reports.download', $report) }}" class="btn btn-primary">
                    <i class="fas fa-download"></i> Download PDF
                </a>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Bar Chart - Category Averages
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            const categoryChart = new Chart(categoryCtx, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach ($summaryData['category_data'] as $category)
                            "{{ $category->category }}",
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Nilai Rata-rata',
                        data: [
                            @foreach ($summaryData['category_data'] as $category)
                                {{ $category->average_rating }},
                            @endforeach
                        ],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 4
                        }
                    }
                }
            });

            // Radar Chart - Category Profile
            const radarCtx = document.getElementById('radarChart').getContext('2d');
            const radarChart = new Chart(radarCtx, {
                type: 'radar',
                data: {
                    labels: [
                        @foreach ($summaryData['category_data'] as $category)
                            "{{ $category->category }}",
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Nilai Rata-rata',
                        data: [
                            @foreach ($summaryData['category_data'] as $category)
                                {{ $category->average_rating }},
                            @endforeach
                        ],
                        fill: true,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgb(54, 162, 235)',
                        pointBackgroundColor: 'rgb(54, 162, 235)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgb(54, 162, 235)'
                    }]
                },
                options: {
                    scales: {
                        r: {
                            angleLines: {
                                display: true
                            },
                            min: 0,
                            max: 4
                        }
                    }
                }
            });

            // Pie Chart - Rating Distribution
            const ratingCtx = document.getElementById('ratingChart').getContext('2d');
            const ratingChart = new Chart(ratingCtx, {
                type: 'pie',
                data: {
                    labels: [
                        @foreach ($summaryData['rating_distribution'] as $rating => $count)
                            "Rating {{ $rating }}",
                        @endforeach
                    ],
                    datasets: [{
                        data: [
                            @foreach ($summaryData['rating_distribution'] as $count)
                                {{ $count }},
                            @endforeach
                        ],
                        backgroundColor: [
                            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'
                        ]
                    }]
                }
            });

            // Pie Chart - Respondent Composition
            const respondentCtx = document.getElementById('respondentChart').getContext('2d');
            const respondentChart = new Chart(respondentCtx, {
                type: 'pie',
                data: {
                    labels: [
                        @foreach ($summaryData['respondents_by_role'] as $role => $count)
                            "{{ ucfirst($role) }}",
                        @endforeach
                    ],
                    datasets: [{
                        data: [
                            @foreach ($summaryData['respondents_by_role'] as $count)
                                {{ $count }},
                            @endforeach
                        ],
                        backgroundColor: [
                            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'
                        ]
                    }]
                }
            });
        });
    </script>
@endsection
