@extends('layouts.app')

@section('title', 'Detail Riwayat Kuesioner - ' . $questionnaire->title)

@section('content')
    <div class="container py-4">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 fw-bold text-orange">
                    <i class="fas fa-clipboard-check me-2"></i> Detail Pengisian Kuesioner
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('responses.history') }}">Riwayat Kuesioner</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $questionnaire->title }}</li>
                    </ol>
                </nav>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('responses.history') }}" class="btn btn-orange">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Riwayat
                </a>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-orange text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="h4 mb-0 fw-bold">{{ $questionnaire->title }}</h2>
                        <p class="mb-0 opacity-75">{{ $questionnaire->description }}</p>
                    </div>
                    <div class="badge bg-white text-orange">
                        {{ strtoupper(str_replace('_', ' ', $questionnaire->type)) }}
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <span class="fw-bold text-muted">Periode Akademik:</span>
                            <span>{{ $questionnaire->academicPeriod->name }}</span>
                        </div>
                        <div>
                            <span class="fw-bold text-muted">Tanggal Pengisian:</span>
                            <span>{{ \Carbon\Carbon::parse($submittedAt)->format('d F Y, H:i') }}</span>
                        </div>
                    </div>
                </div>

                <div class="alert alert-orange mb-4">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle fs-4 mt-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="alert-heading fw-bold">Detail Jawaban Anda</h5>
                            <p class="mb-0">
                                Berikut adalah jawaban yang telah Anda berikan pada kuesioner ini.
                                Terima kasih atas partisipasi Anda dalam meningkatkan kualitas layanan FIK UPNVJ.
                            </p>
                        </div>
                    </div>
                </div>

                @foreach ($questionsByCategory as $categoryName => $questions)
                    <!-- Category Section -->
                    <div class="card mb-4 border-0 shadow-sm rounded-3">
                        <div class="card-header bg-orange-light py-3">
                            <h3 class="h5 mb-0 fw-bold text-orange">
                                <i class="fas fa-folder-open me-2"></i>
                                {{ $categoryName }}
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="60%">Pertanyaan</th>
                                            <th width="20%" class="text-center">Nilai</th>
                                            <th width="20%" class="text-center">Komentar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($questions as $question)
                                            <tr>
                                                <td>{{ $question->question }}</td>
                                                <td class="text-center">
                                                    @if (isset($responses[$question->id]))
                                                        @php
                                                            $rating = $responses[$question->id]->rating;
                                                            $labelClass = 'bg-danger';
                                                            if ($rating == 2) {
                                                                $labelClass = 'bg-warning text-dark';
                                                            }
                                                            if ($rating == 3) {
                                                                $labelClass = 'bg-orange';
                                                            }
                                                            if ($rating == 4) {
                                                                $labelClass = 'bg-success';
                                                            }
                                                        @endphp
                                                        <span class="badge {{ $labelClass }} px-3 py-2">{{ $rating }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if (isset($responses[$question->id]) && $responses[$question->id]->comment)
                                                        <button type="button" class="btn btn-sm btn-orange rounded-circle comment-btn"
                                                            data-bs-toggle="tooltip" title="{{ $responses[$question->id]->comment }}">
                                                            <i class="far fa-comment"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if ($suggestion)
                    <!-- Suggestion Section -->
                    <div class="card mb-4 border-0 shadow-sm rounded-3">
                        <div class="card-header bg-orange-light py-3">
                            <h3 class="h5 mb-0 fw-bold text-orange">
                                <i class="fas fa-lightbulb me-2"></i>
                                Saran & Masukan
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="p-3 bg-orange-light rounded-3">
                                <p class="mb-0">{{ $suggestion->content }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
@endsection
