@extends('layouts.pimpinan')

@section('title', 'Hasil Kuesioner - Pimpinan SPM FIK UPNVJ')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Hasil Kuesioner</h1>
                <p class="mb-0 text-muted">Menampilkan hasil pengisian kuesioner dalam bentuk tabel dan grafik</p>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filter Hasil Kuesioner</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('pimpinan.results.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="academic_period_id" class="form-label">Periode Akademik</label>
                            <select class="form-select" id="academic_period_id" name="academic_period_id">
                                <option value="">Semua Periode</option>
                                @foreach ($academicPeriods as $period)
                                    <option value="{{ $period->id }}" {{ $selectedPeriodId == $period->id ? 'selected' : '' }}>
                                        {{ $period->name }} ({{ $period->semester }} {{ $period->year }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="questionnaire_type" class="form-label">Jenis Kuesioner</label>
                            <select class="form-select" id="questionnaire_type" name="questionnaire_type">
                                <option value="">Semua Jenis</option>
                                @foreach ($questionnaireTypes as $value => $label)
                                    <option value="{{ $value }}" {{ $selectedQuestionnaireType == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="status" class="form-label">Status Pengisian</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Semua Status</option>
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $selectedStatus == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="program_studi" class="form-label">Program Studi</label>
                            <select class="form-select" id="program_studi" name="program_studi">
                                <option value="">Semua Program Studi</option>
                                @foreach ($programStudiOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $selectedProdi == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-end mt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Cari
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Hasil Kuesioner</h6>
                <span class="badge bg-primary">{{ count($questionnaireData) }} Kuesioner</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="resultsTable" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Kuesioner</th>
                                <th>Periode</th>
                                <th>Jumlah Responden</th>
                                <th>Status Pengisian</th>
                                <th>Status Evaluasi</th>
                                <th style="width: 100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($questionnaireData as $data)
                                <tr>
                                    <td>{{ $data['questionnaire']->title }}</td>
                                    <td>
                                        {{ $data['questionnaire']->academicPeriod->name }}
                                        <br>
                                        <small class="text-muted">
                                            {{ date('d/m/Y', strtotime($data['questionnaire']->start_date)) }} -
                                            {{ date('d/m/Y', strtotime($data['questionnaire']->end_date)) }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $data['respondent_count'] }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = 'secondary';
                                            if ($data['status'] === 'active') {
                                                $statusClass = 'primary';
                                            } elseif ($data['status'] === 'completed') {
                                                $statusClass = 'success';
                                            } elseif ($data['status'] === 'upcoming') {
                                                $statusClass = 'info';
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">{{ $data['status_text'] }}</span>
                                    </td>
                                    <td>
                                        @if ($data['evaluation_status'] === 'Tersedia')
                                            <span class="badge bg-success">Tersedia</span>
                                        @else
                                            <span class="badge bg-warning">Belum Ada Data</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if ($data['respondent_count'] > 0)
                                                <a href="{{ route('pimpinan.results.show', ['questionnaire' => $data['questionnaire'], 'program_studi' => $selectedProdi]) }}"
                                                    class="btn btn-sm btn-primary" title="Lihat Detail">
                                                    <i class="fas fa-chart-bar"></i>
                                                </a>
                                                <a href="{{ route('pimpinan.results.respondents', ['questionnaire' => $data['questionnaire'], 'program_studi' => $selectedProdi]) }}"
                                                    class="btn btn-sm btn-info" title="Lihat Responden">
                                                    <i class="fas fa-users"></i>
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-primary" disabled title="Belum Ada Data">
                                                    <i class="fas fa-chart-bar"></i>
                                                </button>
                                                <button class="btn btn-sm btn-info" disabled title="Belum Ada Data">
                                                    <i class="fas fa-users"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3">Tidak ada data kuesioner yang sesuai dengan filter</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#resultsTable').DataTable({
                "searching": false,
                "lengthChange": false,
                "paging": true,
                "info": false,
                "responsive": true,
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "info": "Menampilkan _START_ hingga _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Tidak ada entri yang ditemukan",
                    "zeroRecords": "Tidak ada entri yang cocok",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                },
                "order": [
                    [1, 'asc']
                ]
            });
        });
    </script>
@endsection
