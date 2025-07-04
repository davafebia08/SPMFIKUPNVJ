<!-- resources/views/admin/results/respondents.blade.php -->
@extends('layouts.admin')

@section('title', 'Daftar Responden - ' . $questionnaire->title)

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Daftar Responden</h1>
                <p class="mb-0 text-muted">{{ $questionnaire->title }}</p>
            </div>

            <div class="mt-3 mt-md-0">
                <a href="{{ route('admin.results.show', ['questionnaire' => $questionnaire]) }}" class="btn btn-info">
                    <i class="fas fa-chart-bar me-1"></i> Lihat Hasil
                </a>
                <a href="{{ route('admin.results.index') }}" class="btn btn-outline-primary ms-2">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filter Responden</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.results.respondents', $questionnaire) }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="role" class="form-label">Kategori User</label>
                            <select class="form-select" id="role" name="role">
                                <option value="">Semua Kategori</option>
                                @foreach ($roleOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $selectedRole == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="search" class="form-label">Cari</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ $searchQuery ?? '' }}"
                                placeholder="Cari berdasarkan nama, NIM, NIP, program studi, atau institusi">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Cari
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Respondents Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Responden yang Telah Mengisi</h6>
                <span class="badge bg-primary">{{ $respondents->total() }} Responden</span>
            </div>
            <div class="card-body">
                @if ($selectedRole)
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-filter me-2"></i> Menampilkan responden dengan kategori: <strong>{{ $roleOptions[$selectedRole] }}</strong>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="respondentsTable" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px">No</th>
                                <th>Tanggal Pengisian</th>
                                <th>Nama</th>
                                <th>Identitas</th>
                                <th>Info Tambahan</th>
                                <th>Kategori</th>
                                <th style="width: 100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($respondents as $index => $respondent)
                                <tr>
                                    {{-- <td>{{ $respondents->firstItem() + $index }}</td> --}}
                                    <td>
                                        {{-- itteration --}}
                                        {{ $respondents->currentPage() > 1 ? $respondents->perPage() * ($respondents->currentPage() - 1) + $index + 1 : $index + 1 }}

                                    </td>
                                    <td>{{ date('d/m/Y H:i', strtotime($respondent->submitted_at)) }}</td>
                                    <td>
                                        <strong>{{ $respondent->name }}</strong>
                                        @if ($respondent->role === 'mitra')
                                            <br><small class="text-muted">{{ $respondent->jabatan ?? 'Jabatan tidak diisi' }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($respondent->role === 'mahasiswa')
                                            <span class="badge bg-primary px-2 py-1">NIM</span> {{ $respondent->nim ?? '-' }}
                                        @elseif($respondent->role === 'dosen' || $respondent->role === 'tendik')
                                            <span class="badge bg-info px-2 py-1">NIP</span> {{ $respondent->nip ?? '-' }}
                                        @elseif($respondent->role === 'mitra')
                                            <span class="badge bg-warning px-2 py-1">MITRA</span>
                                            {{ $respondent->nama_instansi ?? 'Institusi tidak diisi' }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($respondent->role === 'mitra')
                                            @if ($respondent->nama_instansi)
                                                <i class="fas fa-building text-primary me-1"></i>{{ $respondent->nama_instansi }}
                                            @else
                                                <span class="text-muted">Institusi tidak diisi</span>
                                            @endif
                                        @elseif(in_array($respondent->role, ['mahasiswa', 'dosen', 'alumni']))
                                            @if ($respondent->program_studi)
                                                <i class="fas fa-graduation-cap text-success me-1"></i>{{ $respondent->program_studi }}
                                            @else
                                                <span class="text-muted">Program studi tidak diisi</span>
                                            @endif
                                        @elseif($respondent->role === 'pengguna_lulusan')
                                            @if ($respondent->nama_instansi)
                                                <i class="fas fa-briefcase text-info me-1"></i>{{ $respondent->nama_instansi }}
                                            @else
                                                <span class="text-muted">Institusi tidak diisi</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $roleLabels = [
                                                'mahasiswa' => 'Mahasiswa',
                                                'dosen' => 'Dosen',
                                                'tendik' => 'Tenaga Kependidikan',
                                                'alumni' => 'Alumni',
                                                'pengguna_lulusan' => 'Pengguna Lulusan',
                                                'mitra' => 'Mitra',
                                            ];

                                            $roleLabel = $roleLabels[$respondent->role] ?? ucfirst($respondent->role);

                                            $roleClass = 'secondary';
                                            if ($respondent->role === 'mahasiswa') {
                                                $roleClass = 'primary';
                                            } elseif ($respondent->role === 'dosen') {
                                                $roleClass = 'info';
                                            } elseif ($respondent->role === 'tendik') {
                                                $roleClass = 'warning';
                                            } elseif ($respondent->role === 'alumni') {
                                                $roleClass = 'success';
                                            } elseif ($respondent->role === 'mitra') {
                                                $roleClass = 'danger';
                                            } elseif ($respondent->role === 'pengguna_lulusan') {
                                                $roleClass = 'dark';
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $roleClass }} px-3 py-2">{{ $roleLabel }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.results.respondent-detail', [$questionnaire->id, $respondent->id]) }}"
                                                class="btn btn-sm btn-primary" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.results.delete-response', [$questionnaire->id, $respondent->id]) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger btn-delete" title="Hapus Data">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Belum ada responden</h5>
                                            <p class="text-muted">Belum ada responden yang mengisi kuesioner ini</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $respondents->appends(request()->query())->links('vendor.pagination.default') }}
                </div>
            </div>
        </div>

        <!-- Summary Statistics untuk Mitra -->
        @if ($questionnaire->type === 'kepuasan_mitra')
            @php
                $mitraStats = \Illuminate\Support\Facades\DB::table('questionnaire_user')
                    ->join('users', 'questionnaire_user.user_id', '=', 'users.id')
                    ->where('questionnaire_user.questionnaire_id', $questionnaire->id)
                    ->whereNotNull('questionnaire_user.submitted_at')
                    ->where('users.role', 'mitra')
                    ->select(
                        \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'),
                        \Illuminate\Support\Facades\DB::raw('COUNT(DISTINCT users.meta_jenis_mitra) as jenis_count'),
                        \Illuminate\Support\Facades\DB::raw('COUNT(DISTINCT users.meta_lingkup_kerjasama) as lingkup_count'),
                    )
                    ->first();

                $jenisDistribution = \Illuminate\Support\Facades\DB::table('questionnaire_user')
                    ->join('users', 'questionnaire_user.user_id', '=', 'users.id')
                    ->where('questionnaire_user.questionnaire_id', $questionnaire->id)
                    ->whereNotNull('questionnaire_user.submitted_at')
                    ->where('users.role', 'mitra')
                    ->whereNotNull('users.meta_jenis_mitra')
                    ->select('users.meta_jenis_mitra', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
                    ->groupBy('users.meta_jenis_mitra')
                    ->get();
            @endphp

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-chart-pie me-2"></i>Statistik Mitra Kerjasama
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-left-danger h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Total Mitra
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $mitraStats->total ?? 0 }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-handshake fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-left-warning h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Jenis Mitra
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $mitraStats->jenis_count ?? 0 }} Jenis
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-left-info h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Lingkup Kerjasama
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $mitraStats->lingkup_count ?? 0 }} Jenis
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-project-diagram fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($jenisDistribution->isNotEmpty())
                        <div class="mt-4">
                            <h6 class="text-dark mb-3">Distribusi Jenis Mitra:</h6>
                            <div class="row">
                                @foreach ($jenisDistribution as $jenis)
                                    <div class="col-md-3 mb-2">
                                        <div class="card bg-light">
                                            <div class="card-body py-2 px-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="small">{{ $jenis->meta_jenis_mitra }}</span>
                                                    <span class="badge bg-primary">{{ $jenis->count }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <style>
        .border-left-danger {
            border-left: 4px solid #dc3545 !important;
        }

        .border-left-warning {
            border-left: 4px solid #ffc107 !important;
        }

        .border-left-info {
            border-left: 4px solid #17a2b8 !important;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Manual submit on role change (for convenience)
            $('#role').change(function() {
                if (!$('#search').val()) { // Only auto-submit if search is empty
                    $(this).closest('form').submit();
                }
            });

            // Confirm delete
            $('.btn-delete').click(function(e) {
                if (!confirm('Apakah Anda yakin ingin menghapus data responden ini?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endsection
