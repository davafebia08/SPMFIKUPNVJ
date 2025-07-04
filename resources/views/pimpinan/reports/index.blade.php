@extends('layouts.pimpinan')

@section('title', 'Laporan Kuesioner')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Laporan Kuesioner</h1>
        </div>

        <!-- Filter Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filter Periode</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('pimpinan.reports.index') }}" class="row align-items-end">
                    <div class="col-md-4">
                        <label for="academic_period_id" class="form-label">Periode Akademik</label>
                        <select name="academic_period_id" id="academic_period_id" class="form-select">
                            <option value="">Semua Periode</option>
                            @foreach ($academicPeriods as $period)
                                <option value="{{ $period->id }}" {{ request('academic_period_id') == $period->id ? 'selected' : '' }}>
                                    {{ $period->name }}
                                    @if ($period->is_active)
                                        <span class="badge bg-success">(Aktif)</span>
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                    @if (request('academic_period_id'))
                        <div class="col-md-4">
                            <div class="alert alert-info mb-0" role="alert">
                                <small>
                                    <i class="fas fa-info-circle"></i>
                                    Menampilkan {{ $questionnaires->count() }} kuesioner dari periode:
                                    <strong>{{ $selectedPeriod->name ?? 'Tidak diketahui' }}</strong>
                                </small>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-danger">Daftar Laporan Kuesioner</h6>
                <small class="text-muted">Total: {{ $questionnaires->count() }} kuesioner</small>
            </div>
            <div class="card-body">
                @if ($questionnaires->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead class="text-center table-danger">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 20%;">Judul Kuesioner</th>
                                    <th style="width: 15%;">Periode Akademik</th>
                                    <th style="width: 20%;">Deskripsi</th>
                                    <th style="width: 10%;">Tanggal Dibuat</th>
                                    <th style="width: 10%;">Jumlah Responden</th>
                                    <th style="width: 20%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($questionnaires as $index => $questionnaire)
                                    <tr>
                                        <td class="text-center align-middle">{{ $index + 1 }}</td>
                                        <td class="align-middle">
                                            {{ $questionnaire->title }}
                                            @if ($questionnaire->is_active)
                                                <span class="badge bg-success ms-1">Aktif</span>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            <small>
                                                {{ $questionnaire->academicPeriod->name ?? '-' }}
                                                @if ($questionnaire->academicPeriod && $questionnaire->academicPeriod->is_active)
                                                    <br><span class="badge bg-primary">Periode Aktif</span>
                                                @endif
                                            </small>
                                        </td>
                                        <td class="align-middle">
                                            {{ Str::limit($questionnaire->description, 100) }}
                                        </td>
                                        <td class="text-center align-middle">{{ $questionnaire->created_at->format('d M Y') }}</td>
                                        <td class="text-center align-middle">
                                            <span class="badge bg-info">{{ $questionnaire->respondent_count ?? 0 }}</span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="d-flex flex-column gap-1">
                                                <a href="{{ route('pimpinan.reports.show', $questionnaire->id) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </a>
                                                <a href="{{ route('pimpinan.reports.print', ['report' => $questionnaire->id, 'orientation' => 'portrait']) }}"
                                                    class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-print"></i> Cetak Laporan
                                                </a>
                                                <a href="{{ route('pimpinan.reports.export-excel', $questionnaire->id) }}"
                                                    class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-file-excel"></i> Export to Excel
                                                </a>
                                                <a href="{{ route('pimpinan.reports.print', ['report' => $questionnaire->id, 'orientation' => 'landscape']) }}"
                                                    class="btn btn-sm btn-outline-secondary" target="_blank">
                                                    <i class="fas fa-print"></i> Cetak Lampiran
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada kuesioner ditemukan</h5>
                        @if (request('academic_period_id'))
                            <p class="text-muted">Tidak ada kuesioner untuk periode yang dipilih.</p>
                            <a href="{{ route('pimpinan.reports.index') }}" class="btn btn-primary">
                                <i class="fas fa-undo"></i> Lihat Semua Periode
                            </a>
                        @else
                            <p class="text-muted">Belum ada kuesioner yang tersedia untuk dibuat laporan.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            @if ($questionnaires->count() > 0)
                $('#dataTable').DataTable({
                    "searching": true,
                    "lengthChange": false,
                    "paging": true,
                    "info": false,
                    "responsive": true,
                    "order": [
                        [4, "desc"]
                    ], // Sort by tanggal dibuat descending
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
                });
            @endif
        });
    </script>
@endpush
