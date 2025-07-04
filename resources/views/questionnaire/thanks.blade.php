@extends('layouts.app')

@section('title', 'Terima Kasih - SPM FIK UPNVJ')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-body p-5">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success fa-5x"></i>
                        </div>

                        <h1 class="h3 fw-bold mb-3">Terima Kasih Atas Partisipasi Anda!</h1>

                        <p class="lead mb-4">
                            Respons Anda telah berhasil disimpan. Masukan Anda sangat berarti untuk peningkatan kualitas layanan
                            Fakultas Ilmu Komputer UPN "Veteran" Jakarta.
                        </p>

                        <div class="alert alert-info mb-4">
                            <p class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Anda dapat menutup halaman ini sekarang.
                            </p>
                        </div>

                        <a href="{{ route('home') }}" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-home me-2"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
