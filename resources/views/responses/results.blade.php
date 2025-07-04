@extends('layouts.app')

@section('title', 'Hasil Kuesioner')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Hasil Kuesioner: {{ $questionnaire->title }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('reports.export', $questionnaire) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-file-export"></i> Export
                </a>
                <a href="{{ route('reports.print', $questionnaire) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                    <i class="fas fa-print"></i> Print
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Rata-rata SPM</h5>
                    <p class="card-text display-4">{{ number_format($averageTotal, 2) }}</p>
                    <p class="card-text">Dari skala 1-4</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Total Responden</h5>
                    <p class="card-text display-4">{{ $totalRespondents }}</p>
                    <p class="card-text">Orang</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Periode Akademik</h5>
                    <p class="card-text display-4">{{ $questionnaire->academicPeriod->name }}</p>
                    <p class="card-text">{{ $questionnaire->start_date->format('d/m/Y') }} - {{ $questionnaire->end_date->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Nilai Rata-rata Per Kategori
                </div>
                <div class="card-body">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="card-footer">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Nilai Rata-rata</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categoryAverages as $category)
                                    <tr>
                                        <td>{{ $category->category }}</td>
                                        <td>{{ number_format($category->average_rating, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Distribusi Rating
                </div>
                <div class="card-body">
                    <canvas id="ratingChart"></canvas>
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
                                    $totalRatings = $ratingDistribution->sum('count');
                                @endphp
                                @foreach ($ratingDistribution as $rating)
                                    <tr>
                                        <td>{{ $rating->rating }}</td>
                                        <td>{{ $rating->count }}</td>
                                        <td>{{ number_format(($rating->count / $totalRatings) * 100, 2) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Komposisi Responden
                </div>
                <div class="card-body">
                    <canvas id="respondentPieChart"></canvas>
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
                                    $totalRespondersByRole = $respondentsByRole->sum('count');
                                @endphp
                                @foreach ($respondentsByRole as $role)
                                    <tr>
                                        <td>{{ ucfirst($role->role) }}</td>
                                        <td>{{ $role->count }}</td>
                                        <td>{{ number_format(($role->count / $totalRespondersByRole) * 100, 2) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Profil Aspek SPM
                </div>
                <div class="card-body">
                    <canvas id="radarChart"></canvas>
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
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="50%">Pertanyaan</th>
                            <th width="15%">Kategori</th>
                            <th width="15%">Rata-rata</th>
                            <th width="15%">Jumlah Responden</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($questionAverages as $index => $question)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $question->question }}</td>
                                <td>{{ $question->category }}</td>
                                <td>{{ number_format($question->average_rating, 2) }}</td>
                                <td>{{ $question->total_responses }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(function() {
            // Radar Chart - Category Profile
            const radarCtx = document.getElementById('radarChart').getContext('2d');
            const radarChart = new Chart(radarCtx, {
                type: 'radar',
                data: {
                    labels: [
                        @foreach ($categoryAverages as $category)
                            '{{ $category->category }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Nilai Rata-rata',
                        data: [
                            @foreach ($categoryAverages as $category)
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

            // Bar Chart - Category Averages
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            const categoryChart = new Chart(categoryCtx, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach ($categoryAverages as $category)
                            '{{ $category->category }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Nilai Rata-rata',
                        data: [
                            @foreach ($categoryAverages as $category)
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

            // Pie Chart - Rating Distribution
            const ratingCtx = document.getElementById('ratingChart').getContext('2d');
            const ratingChart = new Chart(ratingCtx, {
                type: 'pie',
                data: {
                    labels: [
                        @foreach ($ratingDistribution as $rating)
                            'Rating {{ $rating->rating }}',
                        @endforeach
                    ],
                    datasets: [{
                        data: [
                            @foreach ($ratingDistribution as $rating)
                                {{ $rating->count }},
                            @endforeach
                        ],
                        backgroundColor: [
                            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'
                        ]
                    }]
                }
            });

            // Pie Chart - Respondent Composition
            const respondentCtx = document.getElementById('respondentPieChart').getContext('2d');
            const respondentChart = new Chart(respondentCtx, {
                type: 'pie',
                data: {
                    labels: [
                        @foreach ($respondentsByRole as $role)
                            '{{ ucfirst($role->role) }}',
                        @endforeach
                    ],
                    datasets: [{
                        data: [
                            @foreach ($respondentsByRole as $role)
                                {{ $role->count }},
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
