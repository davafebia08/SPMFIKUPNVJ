@extends('layouts.pimpinan')

@section('title', 'Detail Jawaban Responden - ' . $user->name)

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Detail Jawaban Responden</h1>
                <p class="mb-0 text-muted">{{ $questionnaire->title }}</p>
            </div>

            <div class="mt-3 mt-md-0">
                <a href="{{ route('pimpinan.results.respondents', $questionnaire) }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>
        </div>

        <!-- Respondent Info Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user me-2"></i>Informasi Responden
                </h6>
            </div>
            <div class="card-body">
                @if ($user->role === 'mitra')
                    <!-- Format Khusus untuk Mitra Kerjasama -->
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%"><i class="fas fa-user text-primary me-2"></i>Nama Responden</th>
                                    <td><strong>{{ $user->name }}</strong></td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-envelope text-info me-2"></i>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-building text-warning me-2"></i>Nama Institusi</th>
                                    <td>{{ $user->nama_instansi ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-briefcase text-secondary me-2"></i>Jabatan</th>
                                    <td>{{ $user->jabatan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-handshake text-success me-2"></i>Jenis Mitra</th>
                                    <td>{{ $user->meta_jenis_mitra ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%"><i class="fas fa-project-diagram text-info me-2"></i>Jenis Kerjasama</th>
                                    <td>{{ $user->meta_jenis_kerjasama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-globe text-primary me-2"></i>Lingkup Kerjasama</th>
                                    <td>{{ $user->meta_lingkup_kerjasama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-calendar-alt text-warning me-2"></i>Periode Kerjasama</th>
                                    <td>{{ $user->meta_periode_kerjasama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-phone text-success me-2"></i>No Telepon</th>
                                    <td>{{ $user->no_telepon ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-map-marker-alt text-danger me-2"></i>Alamat</th>
                                    <td>{{ $user->meta_alamat ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Summary Card untuk Mitra (Tanpa Badge) -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-light border-left-primary">
                                <div class="row text-center">
                                    <div class="col-md-2">
                                        <h6 class="text-primary mb-1">Kategori</h6>
                                        <span class="text-dark fw-bold">Mitra Kerjasama</span>
                                    </div>
                                    <div class="col-md-2">
                                        <h6 class="text-primary mb-1">Jenis Mitra</h6>
                                        <span class="text-dark">{{ $user->meta_jenis_mitra ?? 'Tidak Diisi' }}</span>
                                    </div>
                                    <div class="col-md-2">
                                        <h6 class="text-primary mb-1">Jenis Kerjasama</h6>
                                        <span class="text-dark">{{ $user->meta_jenis_kerjasama ?? 'Tidak Diisi' }}</span>
                                    </div>
                                    <div class="col-md-2">
                                        <h6 class="text-primary mb-1">Lingkup</h6>
                                        <span class="text-dark">{{ $user->meta_lingkup_kerjasama ?? 'Tidak Diisi' }}</span>
                                    </div>
                                    <div class="col-md-2">
                                        <h6 class="text-primary mb-1">Periode</h6>
                                        <span class="text-dark">{{ $user->meta_periode_kerjasama ?? 'Tidak Diisi' }}</span>
                                    </div>
                                    <div class="col-md-2">
                                        <h6 class="text-primary mb-1">Waktu Pengisian</h6>
                                        <span class="text-dark">{{ date('d/m/Y H:i', strtotime($submission->submitted_at)) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($user->role === 'pengguna_lulusan')
                    <!-- Format Khusus untuk Pengguna Lulusan -->
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%"><i class="fas fa-user text-primary me-2"></i>Nama Responden</th>
                                    <td><strong>{{ $user->name }}</strong></td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-envelope text-info me-2"></i>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-building text-warning me-2"></i>Nama Perusahaan/Institusi</th>
                                    <td>{{ $user->nama_instansi ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-briefcase text-secondary me-2"></i>Jabatan</th>
                                    <td>{{ $user->jabatan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-phone text-success me-2"></i>No Telepon</th>
                                    <td>{{ $user->no_telepon ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%"><i class="fas fa-user-graduate text-primary me-2"></i>Alumni FIK yang Dinilai</th>
                                    <td>
                                        @if ($user->meta_nama_alumni)
                                            <strong class="text-primary">{{ $user->meta_nama_alumni }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-calendar-check text-success me-2"></i>Tahun Lulus Alumni</th>
                                    <td>{{ $user->meta_tahun_lulus_alumni ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-graduation-cap text-info me-2"></i>Program Studi Alumni</th>
                                    <td>{{ $user->meta_program_studi_alumni ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-clock text-secondary me-2"></i>Waktu Pengisian</th>
                                    <td>{{ date('d F Y, H:i', strtotime($submission->submitted_at)) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Summary Card untuk Pengguna Lulusan (Tanpa Badge) -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-light border-left-dark">
                                <div class="row text-center">
                                    <div class="col-md-2">
                                        <h6 class="text-dark mb-1">Kategori</h6>
                                        <span class="text-dark fw-bold">Pengguna Lulusan</span>
                                    </div>
                                    <div class="col-md-2">
                                        <h6 class="text-dark mb-1">Perusahaan</h6>
                                        <span class="text-dark">{{ $user->nama_instansi ?? 'Tidak Diisi' }}</span>
                                    </div>
                                    <div class="col-md-2">
                                        <h6 class="text-dark mb-1">Jabatan</h6>
                                        <span class="text-dark">{{ $user->jabatan ?? 'Tidak Diisi' }}</span>
                                    </div>
                                    <div class="col-md-2">
                                        <h6 class="text-dark mb-1">Alumni yang Dinilai</h6>
                                        <span class="text-dark">{{ $user->meta_nama_alumni ?? 'Tidak Diisi' }}</span>
                                    </div>
                                    <div class="col-md-2">
                                        <h6 class="text-dark mb-1">Program Studi Alumni</h6>
                                        <span class="text-dark">{{ $user->meta_program_studi_alumni ?? 'Tidak Diisi' }}</span>
                                    </div>
                                    <div class="col-md-2">
                                        <h6 class="text-dark mb-1">Tahun Lulus Alumni</h6>
                                        <span class="text-dark">{{ $user->meta_tahun_lulus_alumni ?? 'Tidak Diisi' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Format Standar untuk Role Lainnya -->
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Nama</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Kategori</th>
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

                                            $roleLabel = $roleLabels[$user->role] ?? ucfirst($user->role);
                                        @endphp
                                        {{ $roleLabel }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                @if ($user->role === 'mahasiswa')
                                    <tr>
                                        <th width="30%">NIM</th>
                                        <td>{{ $user->nim ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Program Studi</th>
                                        <td>{{ $user->program_studi ?? '-' }}</td>
                                    </tr>
                                @elseif($user->role === 'dosen')
                                    <tr>
                                        <th width="30%">NIP</th>
                                        <td>{{ $user->nip ?? '-' }}</td>
                                    </tr>
                                @elseif($user->role === 'alumni')
                                    <tr>
                                        <th>Tahun Angkatan</th>
                                        <td>{{ $user->tahun_angkatan ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Tahun Lulus</th>
                                        <td>{{ $user->tahun_lulus ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Program Studi</th>
                                        <td>{{ $user->program_studi ?? '-' }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Waktu Pengisian</th>
                                    <td>
                                        {{ date('d F Y, H:i', strtotime($submission->submitted_at)) }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Responses Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-clipboard-check me-2"></i>Jawaban Kuesioner
                </h6>
            </div>
            <div class="card-body">
                @foreach ($questionsByCategory as $categoryName => $questions)
                    <h5 class="border-bottom pb-2 text-primary">
                        <i class="fas fa-folder-open me-2"></i>{{ $categoryName }}
                    </h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="70%">Pertanyaan</th>
                                    <th width="15%" class="text-center">Nilai</th>
                                    <th width="15%" class="text-center">Komentar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($questions as $question)
                                    @php
                                        $response = $responses[$question->id] ?? null;
                                        $rating = $response ? $response->rating : null;
                                        $comment = $response ? $response->comment : null;

                                        $ratingClass = 'secondary';
                                        $ratingText = 'Tidak Ada';

                                        if ($rating) {
                                            if ($rating === 4) {
                                                $ratingClass = 'success';
                                                $ratingText = 'Sangat Baik';
                                            } elseif ($rating === 3) {
                                                $ratingClass = 'primary';
                                                $ratingText = 'Baik';
                                            } elseif ($rating === 2) {
                                                $ratingClass = 'warning';
                                                $ratingText = 'Cukup';
                                            } else {
                                                $ratingClass = 'danger';
                                                $ratingText = 'Kurang';
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            @if ($question->is_required)
                                                <span class="text-danger">*</span>
                                            @endif
                                            {{ $question->question }}
                                        </td>
                                        <td class="text-center">
                                            @if ($rating)
                                                <span class="badge bg-{{ $ratingClass }} px-3 py-2">{{ $rating }} -
                                                    {{ $ratingText }}</span>
                                            @else
                                                <span class="badge bg-secondary">Tidak Dijawab</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($comment)
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                    data-bs-target="#commentModal_{{ $question->id }}">
                                                    <i class="far fa-comment"></i>
                                                </button>

                                                <!-- Comment Modal -->
                                                <div class="modal fade" id="commentModal_{{ $question->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Komentar</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                    aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p class="mb-2 fw-bold text-start">Pertanyaan:</p>
                                                                <p class="mb-3 text-start">{{ $question->question }}</p>
                                                                <p class="mb-2 fw-bold text-start">Komentar:</p>
                                                                <p class="text-start">{{ $comment }}</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Saran Mitra Kerjasama (Khusus untuk Mitra) -->
        @if ($user->role === 'mitra')
            @php
                $suggestions = \App\Models\Suggestion::where('questionnaire_id', $questionnaire->id)->where('user_id', $user->id)->get();

                $saranMitra = '';
                $saranKemajuan = '';

                foreach ($suggestions as $suggestion) {
                    if (str_contains($suggestion->content, 'Saran dari Mitra:')) {
                        $saranMitra = str_replace('Saran dari Mitra: ', '', $suggestion->content);
                    } elseif (str_contains($suggestion->content, 'Saran Kemajuan FIK:')) {
                        $saranKemajuan = str_replace('Saran Kemajuan FIK: ', '', $suggestion->content);
                    }
                }
            @endphp

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-comments me-2"></i>Saran dan Masukan Mitra
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light border-left-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-comment-dots me-2"></i>Saran dari Mitra</h6>
                                </div>
                                <div class="card-body">
                                    @if (!empty($saranMitra))
                                        <p class="mb-0">{{ $saranMitra }}</p>
                                    @else
                                        <p class="mb-0 text-muted fst-italic">Tidak ada saran yang diberikan</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light border-left-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Saran Kemajuan FIK</h6>
                                </div>
                                <div class="card-body">
                                    @if (!empty($saranKemajuan))
                                        <p class="mb-0">{{ $saranKemajuan }}</p>
                                    @else
                                        <p class="mb-0 text-muted fst-italic">Tidak ada saran yang diberikan</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif ($user->role === 'pengguna_lulusan')
            @php
                $suggestions = \App\Models\Suggestion::where('questionnaire_id', $questionnaire->id)->where('user_id', $user->id)->get();

                $harapan = '';
                $saran = '';

                foreach ($suggestions as $suggestion) {
                    if (str_contains($suggestion->content, 'Harapan :')) {
                        $harapan = str_replace('Harapan : ', '', $suggestion->content);
                    } elseif (str_contains($suggestion->content, 'Saran :')) {
                        $saran = str_replace('Saran : ', '', $suggestion->content);
                    }
                }
            @endphp
            @if ($suggestion)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-comments me-2"></i>Saran dan Masukan Lulusan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light border-left-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="fas fa-comment-dots me-2"></i>Bagaimana harapan Anda terhadap lulusan FIK UPNVJ?
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if (!empty($harapan))
                                            <p class="mb-0">{{ $harapan }}</p>
                                        @else
                                            <p class="mb-0 text-muted fst-italic">Tidak ada Harapan</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light border-left-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Saran dan masukan untuk FIK UPNVJ?</h6>
                                    </div>
                                    <div class="card-body">
                                        @if (!empty($saran))
                                            <p class="mb-0">{{ $saran }}</p>
                                        @else
                                            <p class="mb-0 text-muted fst-italic">Tidak ada saran yang diberikan</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- Saran Umum untuk Non-Mitra -->
            @if ($suggestion)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-comment me-2"></i>Saran & Masukan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-0">{{ $suggestion->content }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- Delete Response Button -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-danger mb-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian!</strong> Tindakan ini akan menghapus semua data jawaban kuesioner dari responden
                    ini dan tidak dapat dikembalikan.
                </div>
                <form action="{{ route('pimpinan.results.delete-response', [$questionnaire->id, $user->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-delete">
                        <i class="fas fa-trash me-1"></i> Hapus Data Responden
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
        .border-left-success {
            border-left: 4px solid #28a745 !important;
        }

        .border-left-info {
            border-left: 4px solid #17a2b8 !important;
        }

        .border-left-primary {
            border-left: 4px solid #007bff !important;
        }

        .border-left-dark {
            border-left: 4px solid #343a40 !important;
        }
    </style>
@endsection
