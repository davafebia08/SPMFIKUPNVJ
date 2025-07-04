@extends('layouts.app')

@section('title', 'Manajemen Kuesioner')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Manajemen Kuesioner</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('questionnaires.create') }}" class="btn btn-primary me-2">
                <i class="fas fa-plus"></i> Buat Kuesioner Baru
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Filter Kuesioner
        </div>
        <div class="card-body">
            <form action="{{ route('questionnaires.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="academic_period_id" class="form-label">Periode Akademik</label>
                    <select name="academic_period_id" id="academic_period_id" class="form-select">
                        <option value="">Semua Periode</option>
                        @foreach ($academicPeriods as $period)
                            <option value="{{ $period->id }}" {{ $academicPeriodId == $period->id ? 'selected' : '' }}>
                                {{ $period->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="type" class="form-label">Jenis Kuesioner</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">Semua Jenis</option>
                        @foreach ($questionnaireTypes as $value => $label)
                            <option value="{{ $value }}" {{ $type == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="is_active" class="form-label">Status</label>
                    <select name="is_active" id="is_active" class="form-select">
                        <option value="">Semua</option>
                        <option value="1" {{ $isActive === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ $isActive === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Daftar Kuesioner
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Judul</th>
                            <th width="15%">Jenis</th>
                            <th width="15%">Periode</th>
                            <th width="10%">Tanggal Mulai</th>
                            <th width="10%">Tanggal Selesai</th>
                            <th width="5%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($questionnaires->count() > 0)
                            @foreach ($questionnaires as $index => $questionnaire)
                                <tr>
                                    <td>{{ $questionnaires->firstItem() + $index }}</td>
                                    <td>{{ $questionnaire->title }}</td>
                                    <td>{{ $questionnaireTypes[$questionnaire->type] }}</td>
                                    <td>{{ $questionnaire->academicPeriod->name }}</td>
                                    <td>{{ $questionnaire->start_date->format('d/m/Y') }}</td>
                                    <td>{{ $questionnaire->end_date->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge {{ $questionnaire->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $questionnaire->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('questionnaires.show', $questionnaire) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('questionnaires.edit', $questionnaire) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('results.show', $questionnaire) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-chart-bar"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $questionnaire->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal{{ $questionnaire->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Apakah Anda yakin ingin menghapus kuesioner "{{ $questionnaire->title }}"?</p>
                                                        <p class="text-danger">Tindakan ini akan menghapus semua data terkait kuesioner ini dan tidak
                                                            dapat dibatalkan.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <form action="{{ route('questionnaires.destroy', $questionnaire) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data kuesioner.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $questionnaires->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
