<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SPM FIK UPNVJ')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary-color: #FF6B35;
            --primary-dark: #E55627;
            --primary-light: #ffffff;
            --accent-color: #FF9E1B;
            --text-light: #ffffff;
            --text-dark: #2D2D2D;
            --bg-light: #ffffff;
            --border-color: #E0E0E0;
            --card-shadow: 0 4px 12px rgba(255, 107, 53, 0.1);
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: 0 2px 10px rgba(255, 107, 53, 0.2);
        }

        .navbar-brand {
            color: var(--text-light) !important;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .nav-link {
            color: var(--text-light) !important;
            transition: all 0.3s;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 4px;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .dropdown-item:active {
            background-color: var(--primary-color);
            color: white !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transition: all 0.3s;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 107, 53, 0.3);
        }

        .btn-outline-light:hover {
            color: var(--primary-color) !important;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.15);
        }

        .card-header {
            background-color: var(--primary-light);
            border-bottom: none;
            font-weight: 600;
            color: var(--primary-dark);
            padding: 1rem 1.5rem;
        }

        footer {
            margin-top: auto;
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 2rem 0;
        }

        .footer-link {
            color: var(--text-light);
            text-decoration: none;
            transition: opacity 0.3s;
        }

        .footer-link:hover {
            opacity: 0.8;
            color: var(--text-light);
        }

        /* Custom Orange Theme Elements */
        .bg-orange-light {
            background-color: var(--primary-light);
        }

        .bg-orange {
            background-color: #FF6B35;
        }

        .text-orange {
            color: var(--primary-color);
        }

        .border-orange {
            border-color: var(--primary-color);
        }

        /* Modern UI Elements */
        .rounded-xl {
            border-radius: 16px;
        }

        .shadow-orange {
            box-shadow: var(--card-shadow);
        }

        /* Navbar Dropdown */
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 8px;
        }

        .dropdown-item {
            padding: 0.5rem 1.5rem;
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background-color: var(--primary-light);
            color: var(--primary-dark);
        }

        /* Active Nav Item */
        .nav-item.active .nav-link {
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }

        /* Profile Image */
        .profile-img {
            width: 32px;
            height: 32px;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        /* Custom danger */
        .bg-danger {
            background-color: #ff1a1a !important;
            color: #fff;
            /* agar teks tetap terbaca di atas warna merah */
        }
    </style>

    @stack('styles')
    @yield('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ asset('images/logoupn.png') }}" alt="Logo UPN" height="40" class="me-2">
                <span>SPM FIK UPNVJ</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                Kuesioner
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('questionnaires.select') }}"><i class="fas fa-edit me-2"></i>Isi Kuesioner</a>
                                <li><a class="dropdown-item" href="{{ route('responses.history') }}"><i class="fas fa-history me-2"></i>Riwayat
                                        Kuesioner</a></li>
                        </li>
                        @if (auth()->user()->isAdmin())
                            <li><a class="dropdown-item" href="{{ route('questionnaires.index') }}"><i class="fas fa-cog me-2"></i>Kelola
                                    Kuesioner</a></li>
                        @endif
                        {{-- <li><a class="dropdown-item" href="{{ route('results.index') }}"><i class="fas fa-chart-bar me-2"></i>Hasil Kuesioner</a>
                        </li>
                        <li><a class="dropdown-item" href="{{ route('public.questionnaire.index') }}"><i
                                    class="fas fa-external-link-alt me-2"></i>Kuesioner Publik</a></li> --}}
                    </ul>
                    </li>
                    @if (auth()->user()->isAdmin() || auth()->user()->isPimpinan())
                        <li class="nav-item"><a class="nav-link" href="{{ route('reports.index') }}">Laporan</a></li>
                    @endif
                    @if (auth()->user()->isAdmin())
                        <li class="nav-item"><a class="nav-link" href="{{ route('academic-periods.index') }}">Periode Akademik</a></li>
                    @endif
                @endauth
                </ul>
                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item me-2"><a class="nav-link btn btn-outline-light px-3" href="{{ route('register') }}">Registrasi</a></li>
                        <li class="nav-item"><a class="nav-link btn btn-light text-orange px-3 fw-bold" href="{{ route('login') }}">Masuk</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('public.questionnaire.index') }}">Kuesioner Publik</a></li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown">
                                @if (Auth::user()->profile_photo)
                                    <img src="{{ asset('storage/profile-photos/' . Auth::user()->profile_photo) }}" alt="Profile Photo"
                                        class="rounded-circle profile-img me-2">
                                @else
                                    <i class="fas fa-user-circle me-2"></i>
                                @endif
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user-cog me-2"></i>Profil</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        <div class="container my-5">
            @include('partials.alerts')
            @yield('content')
        </div>
    </main>

    <footer class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0">
                    <h5 class="fw-bold mb-3">SPM FIK UPNVJ</h5>
                    <p class="mb-0">Sistem Pelayanan Minimal Fakultas Ilmu Komputer UPN Veteran Jakarta</p>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5 class="fw-bold mb-3">Tautan Cepat</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ url('/') }}" class="footer-link d-inline-block"><i
                                    class="fas fa-chevron-right me-2"></i>Beranda</a></li>
                        <li class="mb-2"><a href="{{ route('dashboard') }}" class="footer-link d-inline-block"><i
                                    class="fas fa-chevron-right me-2"></i>Dashboard</a></li>
                        <li><a href="https://upnvj.ac.id" target="_blank" class="footer-link d-inline-block"><i
                                    class="fas fa-chevron-right me-2"></i>UPNVJ</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5 class="fw-bold mb-3">Kontak</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> Jl. RS Fatmawati No.1, Jakarta</li>
                        <li><i class="fas fa-envelope me-2"></i> fikupnvj@upnvj.ac.id</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 bg-white opacity-25">
            <div class="text-center pt-2">
                <small>&copy; {{ date('Y') }} SPM FIK UPNVJ. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom Scripts -->
    <script>
        // Logout confirmation
        document.getElementById('logoutForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: 'Anda yakin ingin keluar?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#FF6B35',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal',
                background: 'white'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

        // Display flash messages using SweetAlert
        @if (session('success'))
            Swal.fire({
                title: 'Sukses!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#FF6B35',
                background: 'white'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#FF6B35',
                background: 'white'
            });
        @endif
    </script>

    @stack('scripts')
    @yield('scripts')
</body>

</html>
