@extends('layouts.app')

@section('title', 'Pilih Kuesioner - SPM FIK UPNVJ')

@section('content')
    <div class="container py-5">
        <!-- Header with Back Button -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5">
            <div>
                <h1 class="h2 fw-bold text-orange mb-2">
                    <i class="fas fa-clipboard-list me-2"></i> Daftar Kuesioner
                </h1>
                <div class="breadcrumb">
                    <span class="text-muted">
                        Periode Aktif: <span class="fw-semibold">{{ $activePeriod->name }}</span>
                    </span>
                </div>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-orange mt-3 mt-md-0">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>

        <!-- Hero Section -->
        <div class="bg-orange-light rounded-xl p-5 mb-5 text-center position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10">
                <div class="pattern-dots-md" style="color: var(--primary-color);"></div>
            </div>
            <div class="position-relative">
                <h2 class="display-6 fw-bold mb-3 text-orange">Evaluasi Layanan FIK UPNVJ</h2>
                <p class="lead mb-4">
                    Berikan penilaian Anda untuk membantu kami meningkatkan kualitas layanan
                </p>
                <div class="badge bg-white text-orange px-3 py-2 rounded-pill border border-orange">
                    <i class="fas fa-user-tie me-1"></i>
                    Anda login sebagai: {{ ucfirst(Auth::user()->role) }}
                </div>
            </div>
        </div>

        @if ($questionnaires->count() > 0)
            <!-- Questionnaire Cards -->
            <div class="row g-4">
                @foreach ($questionnaires as $questionnaire)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-orange rounded-xl hover-shadow transition-all">
                            <div class="card-header bg-orange text-white py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">{{ $questionnaire->title }}</h5>
                                    <span class="badge bg-white text-orange">
                                        {{ strtoupper($questionnaire->type) }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-4">{{ $questionnaire->description }}</p>

                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-calendar-alt text-orange me-2"></i>
                                    <small class="text-muted">
                                        Periode: {{ $activePeriod->name }}
                                    </small>
                                </div>

                                <div class="d-flex align-items-center mb-4">
                                    <i class="fas fa-clock text-orange me-2"></i>
                                    <small class="text-muted">
                                        Batas akhir: {{ date('d M Y', strtotime($questionnaire->end_date)) }}
                                    </small>
                                </div>

                                @if (in_array($questionnaire->id, $completedQuestionnaires ?? []))
                                    <div class="alert alert-success d-flex align-items-center mb-0 py-2 rounded-xl">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <span>Anda sudah mengisi kuesioner ini</span>
                                    </div>
                                @else
                                    <a href="{{ route('responses.fill', $questionnaire->id) }}" class="btn btn-orange w-100 py-2 rounded-xl">
                                        <i class="fas fa-pen-alt me-2"></i>
                                        Isi Kuesioner
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-5 my-5">
                <div class="mb-4">
                    <img src="{{ asset('images/empty-state.jpg') }}" alt="No questionnaires" style="max-height: 200px;" class="rounded-xl">
                </div>
                <h3 class="h4 fw-bold mb-3 text-orange">Tidak Ada Kuesioner Tersedia</h3>
                <p class="text-muted mb-4">
                    Saat ini tidak ada kuesioner yang perlu Anda isi. Silakan kembali nanti.
                </p>
                <a href="{{ route('dashboard') }}" class="btn btn-orange px-4 rounded-xl">
                    <i class="fas fa-home me-2"></i> Kembali ke Dashboard
                </a>
            </div>
        @endif

        <!-- Footer Note -->
        <div class="mt-5 pt-4 text-center text-muted border-top">
            <small>
                <i class="fas fa-info-circle me-1"></i>
                Setiap kuesioner yang sudah diisi tidak dapat diubah kembali
            </small>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .hover-shadow {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(255, 107, 53, 0.15) !important;
        }

        .btn-outline-orange {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-orange:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-orange {
            background-color: var(--primary-color);
            color: white;
            border: none;
            transition: all 0.3s;
        }

        .btn-orange:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 107, 53, 0.3);
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animation for cards
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.opacity = 0;
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';

                setTimeout(() => {
                    card.style.opacity = 1;
                    card.style.transform = 'translateY(0)';
                }, 100 * index);
            });
        });
    </script>
@endsection
