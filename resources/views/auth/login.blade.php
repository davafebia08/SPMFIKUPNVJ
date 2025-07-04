@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-orange rounded-xl">
                    <div class="card-header bg-orange text-white py-3">
                        <h5 class="mb-0 text-center">Login Sistem SPM FIK UPNVJ</h5>
                    </div>
                    <div class="card-body p-4">

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-4">
                                <label for="email" class="form-label fw-medium">NIM/NIDN/NIK/Email/Username</label>
                                <input type="text" class="form-control rounded-xl @error('email') is-invalid @enderror" id="email" name="email"
                                    value="{{ old('email') }}" required autofocus>
                                <div class="form-text text-muted">Masukkan NIM untuk mahasiswa, NIDN untuk dosen, NIK untuk tendik, atau email/username
                                    jika telah terdaftar.</div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label fw-medium">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control rounded-start @error('password') is-invalid @enderror" id="password"
                                        name="password" required>
                                    <button class="btn btn-outline-secondary rounded-end" type="button" id="togglePassword">
                                        <i class="fas fa-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Ingat Saya</label>
                            </div>
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg rounded-xl py-2 fw-medium">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </button>
                            </div>
                        </form>

                        <div class="mt-4 text-center">
                            <p class="text-muted mb-0">Sistem Pelayanan Minimal (SPM) Fakultas Ilmu Komputer UPN "Veteran" Jakarta</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            togglePassword.addEventListener('click', function() {
                // Toggle tipe input
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Toggle icon
                if (type === 'password') {
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                } else {
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                }
            });
        });
    </script>

@endsection
