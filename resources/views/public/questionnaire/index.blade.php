@extends('layouts.app')

@section('title', 'Kuesioner Publik - SPM FIK UPNVJ')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="text-center mb-5">
                    <img src="{{ asset('images/logoupn.png') }}" alt="Logo FIK UPNVJ" class="img-fluid mb-4" style="max-height: 120px;">
                    <h1 class="h2 fw-bold mb-3 text-orange">Kuesioner Pelayanan Minimal FIK UPNVJ</h1>
                    <p class="lead text-muted">
                        Pilih jenis kuesioner yang akan Anda isi sesuai dengan kategori Anda
                    </p>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-orange rounded-xl hover-card">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="fas fa-building fa-4x text-orange"></i>
                                </div>
                                <h2 class="h4 fw-bold mb-3">Pengguna Lulusan</h2>
                                <p class="mb-4 text-muted">
                                    Kuesioner untuk perusahaan/instansi yang mempekerjakan alumni FIK UPNVJ
                                </p>
                                <a href="{{ route('public.questionnaire.pengguna-lulusan') }}" class="btn btn-orange px-4 rounded-xl">
                                    <i class="fas fa-clipboard-list me-2"></i> Isi Kuesioner
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-orange rounded-xl hover-card">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="fas fa-handshake fa-4x text-orange"></i>
                                </div>
                                <h2 class="h4 fw-bold mb-3">Mitra Kerjasama</h2>
                                <p class="mb-4 text-muted">
                                    Kuesioner untuk institusi yang menjalin kerjasama dengan FIK UPNVJ
                                </p>
                                <a href="{{ route('public.questionnaire.mitra') }}" class="btn btn-orange px-4 rounded-xl">
                                    <i class="fas fa-clipboard-list me-2"></i> Isi Kuesioner
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-orange rounded-xl hover-card">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="fas fa-user-graduate fa-4x text-orange"></i>
                                </div>
                                <h2 class="h4 fw-bold mb-3">Alumni</h2>
                                <p class="mb-4 text-muted">
                                    Jika Anda alumni FIK UPNVJ, silakan login atau daftar untuk mengisi kuesioner
                                </p>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('login') }}" class="btn btn-outline-orange rounded-xl">
                                        <i class="fas fa-sign-in-alt me-2"></i> Login
                                    </a>
                                    <a href="{{ route('register') }}" class="btn btn-orange rounded-xl">
                                        <i class="fas fa-user-plus me-2"></i> Daftar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-orange rounded-xl hover-card">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="fas fa-info-circle fa-4x text-orange"></i>
                                </div>
                                <h2 class="h4 fw-bold mb-3">Informasi</h2>
                                <p class="mb-4 text-muted">
                                    Untuk mahasiswa, dosen, dan tendik dapat mengisi kuesioner melalui akun SIAKAD
                                </p>
                                <a href="{{ route('login') }}" class="btn btn-orange px-4 rounded-xl">
                                    <i class="fas fa-sign-in-alt me-2"></i> Login SIAKAD
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .hover-card {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(255, 107, 53, 0.15) !important;
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

        .btn-outline-orange {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-orange:hover {
            background-color: var(--primary-color);
            color: white;
        }
    </style>
@endsection
