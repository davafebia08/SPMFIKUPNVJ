@extends('layouts.app')

@section('title', 'Detail Kuesioner')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detail Kuesioner</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('questionnaires.edit', $questionnaire) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('results.show', $questionnaire) }}" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-chart-bar"></i> Hasil
                </a>
            </div>
            <a href="{{ route('questionnaires.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Informasi Kuesioner
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th width="30%">Judul</th>
                            <td>{{ $questionnaire->title }}</td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $questionnaire->description ?: 'Tidak ada deskripsi' }}</td>
                        </tr>
                        <tr>
                            <th>Jenis</th>
                            <td>
                                @php
                                    $types = [
                                        'layanan_fakultas' => 'Layanan Fakultas',
                                        'elom' => 'Evaluasi Layanan oleh Mahasiswa (ELOM)',
                                        'evaluasi_dosen' => 'Evaluasi Dosen',
                                        'elta' => 'Evaluasi Layanan Tugas Akhir (ELTA)',
                                        'kepuasan_dosen' => 'Kepuasan Dosen',
                                        'kepuasan_tendik' => 'Kepuasan Tenaga Kependidikan',
                                        'kepuasan_alumni' => 'Kepuasan Alumni',
                                        'kepuasan_pengguna_lulusan' => 'Kepuasan Pengguna Lulusan',
                                        'kepuasan_mitra' => 'Kepuasan Mitra Kerjasama',
                                    ];
                                @endphp
                                {{ $types[$questionnaire->type] ?? $questionnaire->type }}
                            </td>
                        </tr>
                        <tr>
                            <th>Periode Akademik</th>
                            <td>{{ $questionnaire->academicPeriod->name }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>{{ $questionnaire->start_date->format('d/m/Y') }} - {{ $questionnaire->end_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge {{ $questionnaire->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $questionnaire->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Kategori Responden
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @php
                            $roles = [
                                'mahasiswa' => 'Mahasiswa',
                                'dosen' => 'Dosen',
                                'tendik' => 'Tenaga Kependidikan',
                                'alumni' => 'Alumni',
                                'pengguna_lulusan' => 'Pengguna Lulusan',
                                'mitra' => 'Mitra Kerjasama',
                                'admin' => 'Administrator',
                                'pimpinan' => 'Pimpinan Fakultas',
                            ];
                        @endphp

                        @foreach ($questionnaire->permissions as $permission)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $roles[$permission->role] ?? $permission->role }}
                                <span>
                                    @if ($permission->can_fill)
                                        <span class="badge bg-primary">Dapat Mengisi</span>
                                    @endif

                                    @if ($permission->can_view_results)
                                        <span class="badge bg-info">Dapat Melihat Hasil</span>
                                    @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @foreach ($questionsByCategory as $categoryName => $questions)
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">{{ $categoryName }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="70%">Pertanyaan</th>
                                <th width="15%">Status</th>
                                <th width="10%">Wajib</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($questions as $index => $question)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $question->question }}</td>
                                    <td>
                                        <span class="badge {{ $question->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $question->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $question->is_required ? 'bg-primary' : 'bg-secondary' }}">
                                            {{ $question->is_required ? 'Wajib' : 'Opsional' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
@endsection
