@extends('layouts.app')

@section('title', 'Terima Kasih - SPM FIK UPNVJ')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="card border-0 shadow-orange rounded-xl overflow-hidden">
                    <div class="card-body p-5">
                        <div class="mb-4">
                            <div class="bg-orange-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 100px; height: 100px;">
                                <i class="fas fa-check-circle fa-4x text-orange"></i>
                            </div>
                        </div>

                        <h1 class="h2 fw-bold text-orange mb-3">Terima Kasih!</h1>

                        <p class="lead mb-4">
                            Respons kuesioner Anda telah berhasil tersimpan. Masukan Anda sangat berarti bagi kami dalam meningkatkan kualitas pelayanan.
                        </p>

                        <div class="alert alert-light border-orange bg-orange-light text-dark mb-4">
                            <p class="mb-0">Data Anda akan kami proses dan dijaga kerahasiaannya. Hasil dari kuesioner ini akan digunakan sebagai bahan
                                evaluasi dan perbaikan berkelanjutan.</p>
                        </div>

                        <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                            <a href="{{ route('public.questionnaire.index') }}" class="btn btn-outline-orange px-4 rounded-xl">
                                <i class="fas fa-clipboard-list me-2"></i> Kembali ke Halaman Kuesioner
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-orange px-4 rounded-xl">
                                <i class="fas fa-home me-2"></i> Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="text-muted">
                        Jika ada pertanyaan lebih lanjut, silakan hubungi kami di:<br>
                        <a href="mailto:fik@upnvj.ac.id" class="text-orange">fik@upnvj.ac.id</a> | <a href="tel:+622178835283"
                            class="text-orange">+62-21-7883 5283</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .bg-orange-light {
            background-color: var(--primary-light);
        }

        .text-orange {
            color: var(--primary-color);
        }

        .shadow-orange {
            box-shadow: 0 0.5rem 1rem rgba(255, 107, 53, 0.15);
        }

        .border-orange {
            border-color: var(--primary-color) !important;
        }

        .btn-orange {
            background-color: var(--primary-color);
            color: white;
            border: none;
            transition: all 0.3s;
        }

        .btn-orange:hover {
            background-color: var(--primary-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 107, 53, 0.3);
        }

        .btn-outline-orange {
            color: var(--primary-color);
            border-color: var(--primary-color);
            transition: all 0.3s;
        }

        .btn-outline-orange:hover {
            background-color: var(--primary-color);
            color: white;
        }
    </style>
@endsection
