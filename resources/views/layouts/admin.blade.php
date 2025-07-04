<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - SPM FIK UPNVJ')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Admin Custom CSS -->
    <style>
        :root {
            --primary-color: #FF6B35;
            --primary-dark: #E55627;
            --primary-light: #FFF3EB;
            --accent-color: #FF9E1B;
            --text-light: #ffffff;
            --text-dark: #2D2D2D;
            --bg-light: #ffffff;
            --border-color: #E0E0E0;
            --card-shadow: 0 4px 12px rgba(255, 107, 53, 0.1);
        }

        body {
            background-color: #f8f9fc;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
        }

        #wrapper {
            width: 100%;
            display: flex;
        }

        /* Sidebar */
        #sidebar {
            width: 280px;
            background-color: var(--primary-color);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
            transition: all 0.3s;
        }

        #sidebar.collapsed {
            margin-left: -280px;
        }

        #sidebar .sidebar-brand {
            padding: 1.5rem;
            text-align: center;
            color: var(--text-light);
            font-weight: 700;
            font-size: 1.25rem;
        }

        #sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 1rem 1.5rem;
            transition: all 0.3s;
            font-weight: 500;
            position: relative;
        }

        #sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        #sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.15);
            box-shadow: inset 4px 0 0 white;
        }

        #sidebar .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }

        /* Content */
        #content {
            width: calc(100% - 280px);
            margin-left: 280px;
            flex: 1;
            transition: all 0.3s;
        }

        #content.expanded {
            width: 100%;
            margin-left: 0;
        }

        /* Topbar */
        #topbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #sidebarToggle {
            background-color: transparent;
            border: none;
            color: var(--text-dark);
            font-size: 1.25rem;
            cursor: pointer;
            transition: transform 0.3s;
        }

        #sidebarToggle:hover {
            transform: scale(1.1);
        }

        /* Main Content */
        .main-content {
            padding: 1.5rem;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 0.75rem 1.25rem;
        }

        .card-body {
            padding: 1.25rem;
        }

        /* Border Left Utilities */
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .border-left-danger {
            border-left: 0.25rem solid #e74a3b !important;
        }

        /* User Dropdown */
        .user-dropdown {
            position: relative;
        }

        .user-dropdown .dropdown-toggle {
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .user-dropdown .dropdown-toggle::after {
            display: none;
        }

        .user-dropdown .dropdown-menu {
            position: absolute;
            right: 0;
            left: auto;
            margin-top: 0.5rem;
            border: none;
            box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15);
            border-radius: 0.35rem;
        }

        .user-dropdown .dropdown-item {
            padding: 0.5rem 1rem;
            transition: all 0.2s;
        }

        .user-dropdown .dropdown-item:hover {
            background-color: #f8f9fc;
            color: var(--primary-color);
        }

        .dropdown-divider {
            margin: 0.5rem 0;
            border-top: 1px solid #e3e6f0;
        }

        .user-profile-img {
            width: 36px;
            height: 36px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid rgba(255, 107, 53, 0.3);
            margin-right: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -280px;
            }

            #sidebar.collapsed {
                margin-left: 0;
            }

            #content {
                width: 100%;
                margin-left: 0;
            }

            #content.expanded {
                width: 100%;
                margin-left: 0;
            }
        }

        /* Custom Pagination Styles */
        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 1.5rem 0;
            border-radius: 0.375rem;
        }

        .pagination li {
            display: inline-block;
            margin: 0 2px;
        }

        .pagination li a,
        .pagination li span {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 12px;
            color: #333;
            background-color: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .pagination li a:hover {
            color: #FF6B35;
            background-color: #FFF3EB;
            border-color: #FF6B35;
        }

        .pagination li.active span {
            color: #fff;
            background-color: #FF6B35;
            border-color: #FF6B35;
        }

        .pagination li.disabled span {
            color: #aaa;
            background-color: #f8f9fa;
            border-color: #e0e0e0;
            cursor: not-allowed;
        }

        /* Add arrow icons for next/previous */
        .pagination li:first-child a::before,
        .pagination li:first-child span::before {
            content: "«";
            margin-right: 4px;
        }

        .pagination li:last-child a::after,
        .pagination li:last-child span::after {
            content: "»";
            margin-left: 4px;
        }
    </style>

    @stack('styles')
    @yield('styles')
</head>

<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-brand">
                <img src="{{ asset('images/logoupn.png') }}" alt="Logo UPN" height="40" class="me-2">
                SPM FIK UPNVJ
            </div>
            <hr class="sidebar-divider bg-white opacity-25 my-0">

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.questionnaires.*') ? 'active' : '' }}"
                        href="{{ route('admin.questionnaires.index') }}">
                        <i class="fas fa-fw fa-clipboard-list"></i>
                        <span>Kelola Kuesioner</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.results.*') ? 'active' : '' }}" href="{{ route('admin.results.index') }}">
                        <i class="fas fa-fw fa-chart-bar"></i>
                        <span>Hasil Kuesioner</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.respondent-categories.*') ? 'active' : '' }}"
                        href="{{ route('admin.respondent-categories.index') }}">
                        <i class="fas fa-fw fa-users"></i>
                        <span>Kategori Responden</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}" href="{{ route('admin.schedules.index') }}">
                        <i class="fas fa-fw fa-calendar-alt"></i>
                        <span>Kelola Jadwal Kuesioner</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                        <i class="fas fa-fw fa-file-alt"></i>
                        <span>Laporan</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.import.*') ? 'active' : '' }}" href="{{ route('admin.import.index') }}">
                        <i class="fas fa-fw fa-file-import"></i>
                        <span>Import Data</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <i class="fas fa-fw fa-user-cog"></i>
                        <span>Kelola User</span>
                    </a>
                </li>
            </ul>

            <hr class="sidebar-divider bg-white opacity-25 d-none d-md-block">
            <div class="text-center text-white-50 small p-3">
                &copy; {{ date('Y') }} SPM FIK UPNVJ
            </div>
        </nav>

        <!-- Content Wrapper -->
        <div id="content">
            <!-- Topbar -->
            <nav id="topbar" class="navbar navbar-light bg-white mb-4 static-top shadow-sm">
                <div class="d-flex align-items-center">
                    <button id="sidebarToggle" class="me-3">
                        <i class="fas fa-bars"></i>
                    </button>
                    {{-- <span
                        class="d-none d-md-inline-block">{{ (auth()->user()->isAdmin() ? 'Administrator' : auth()->user()->isPimpinan()) ? 'Pimpinan' : 'User' }}</span> --}}
                </div>

                <div class="user-dropdown dropdown">
                    <a class="dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        @if (auth()->user()->profile_photo)
                            <img src="{{ auth()->user()->getProfilePhotoUrlAttribute() }}" alt="Profile" class="user-profile-img">
                        @else
                            <i class="fas fa-user-circle fa-fw me-2"></i>
                        @endif
                        <span class="d-none d-md-inline-block">{{ auth()->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i
                                    class="fas fa-user-edit fa-sm fa-fw me-2 text-gray-400"></i> Profil</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="main-content">
                @include('partials.alerts')
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Admin Custom Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    content.classList.toggle('expanded');
                });
            }

            // Handle Logout confirmation
            const logoutForm = document.querySelector('form[action="{{ route('logout') }}"]');
            if (logoutForm) {
                logoutForm.addEventListener('submit', function(e) {
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
            }

            // Handle Delete confirmation
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Konfirmasi Hapus',
                        text: 'Anda yakin ingin menghapus data ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        background: 'white'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
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
        });
    </script>

    @stack('scripts')
    @yield('scripts')
</body>

</html>
