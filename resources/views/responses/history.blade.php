@extends('layouts.app')

@section('title', 'Riwayat Pengisian Kuesioner - SPM FIK UPNVJ')

@section('content')
    <div class="container py-4">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 fw-bold text-orange">
                    <i class="fas fa-history me-2"></i> Riwayat Kuesioner
                </h1>
                <p class="text-muted mb-0">
                    Daftar kuesioner yang telah Anda isi sebelumnya
                </p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-orange rounded-xl">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>

        @if ($completedQuestionnaires->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-orange rounded-xl overflow-hidden">
                        <div class="card-header bg-orange text-white py-3">
                            <h2 class="h5 mb-0 fw-bold">
                                <i class="fas fa-clipboard-check me-2"></i> Kuesioner yang Telah Diisi
                            </h2>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Judul Kuesioner</th>
                                            <th>Jenis</th>
                                            <th>Periode</th>
                                            <th>Waktu Pengisian</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($completedQuestionnaires as $questionnaire)
                                            <tr>
                                                <td class="fw-medium">{{ $questionnaire->title }}</td>
                                                <td>
                                                    <span class="badge bg-orange text-white">
                                                        {{ ucfirst(str_replace('_', ' ', $questionnaire->type)) }}
                                                    </span>
                                                </td>
                                                <td>{{ $questionnaire->academicPeriod->name }}</td>
                                                <td>
                                                    @if (isset($questionnaire->pivot->submitted_at))
                                                        {{ \Carbon\Carbon::parse($questionnaire->pivot->submitted_at)->format('d M Y, H:i') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('responses.history.detail', $questionnaire) }}"
                                                        class="btn btn-sm btn-orange rounded-xl">
                                                        <i class="fas fa-eye me-1"></i> Detail
                                                    </a>
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
        @else
            <div class="card border-0 shadow-orange rounded-xl">
                <div class="card-body p-5 text-center">
                    <div class="py-4">
                        <i class="fas fa-clipboard fa-4x text-muted mb-3"></i>
                        <h3 class="h4 fw-bold mb-3 text-orange">Belum Ada Riwayat Kuesioner</h3>
                        <p class="mb-4">Anda belum pernah mengisi kuesioner apapun.</p>
                        <a href="{{ route('questionnaires.select') }}" class="btn btn-orange px-4 rounded-xl">
                            <i class="fas fa-clipboard-list me-2"></i> Isi Kuesioner Sekarang
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('styles')
    <style>
        .table-hover tbody tr:hover {
            background-color: rgba(255, 107, 53, 0.05);
        }
    </style>
@endsection
