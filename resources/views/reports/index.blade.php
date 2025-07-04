@extends('layouts.app')

@section('title', 'Laporan SPM')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Laporan SPM</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateReportModal">
                <i class="fas fa-file-export"></i> Buat Laporan Baru
            </button>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Filter Laporan
        </div>
        <div class="card-body">
            <form action="{{ route('reports.index') }}" method="GET" class="row g-3">
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
                    <label for="questionnaire_type" class="form-label">Jenis Kuesioner</label>
                    <select name="questionnaire_type" id="questionnaire_type" class="form-select">
                        <option value="">Semua Jenis</option>
                        @foreach ($questionnaireTypes as $value => $label)
                            <option value="{{ $value }}" {{ $questionnaireType == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-success w-100" id="export-all-btn">
                        <i class="fas fa-file-excel"></i> Export Semua
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Daftar Laporan SPM
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Periode Akademik</th>
                            <th width="25%">Jenis Kuesioner</th>
                            <th width="15%">Tanggal Dibuat</th>
                            <th width="15%">Dibuat Oleh</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $index => $report)
                            <tr>
                                <td>{{ $reports->firstItem() + $index }}</td>
                                <td>{{ $report->academicPeriod->name }}</td>
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
                                    {{ $types[$report->questionnaire->type] ?? $report->questionnaire->type }}
                                </td>
                                <td>{{ $report->generated_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $report->generator->name }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-info" target="_blank">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                        <a href="{{ route('reports.download', $report) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteReportModal{{ $report->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteReportModal{{ $report->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Apakah Anda yakin ingin menghapus laporan ini?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <form action="{{ route('reports.destroy', $report) }}" method="POST">
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
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data laporan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $reports->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- Generate Report Modal -->
    <div class="modal fade" id="generateReportModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('reports.generate') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Buat Laporan Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="generate_academic_period_id" class="form-label">Periode Akademik <span class="text-danger">*</span></label>
                            <select class="form-select" id="generate_academic_period_id" name="academic_period_id" required>
                                <option value="">Pilih Periode Akademik</option>
                                @foreach ($academicPeriods as $period)
                                    <option value="{{ $period->id }}">{{ $period->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="generate_questionnaire_id" class="form-label">Kuesioner <span class="text-danger">*</span></label>
                            <select class="form-select" id="generate_questionnaire_id" name="questionnaire_id" required>
                                <option value="">Pilih Kuesioner</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Tambahkan catatan tentang laporan ini (opsional)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Generate Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Dynamic Questionnaire Select based on Academic Period
            $('#generate_academic_period_id').change(function() {
                const academicPeriodId = $(this).val();
                if (!academicPeriodId) {
                    $('#generate_questionnaire_id').empty().append('<option value="">Pilih Kuesioner</option>');
                    return;
                }

                // Fetch questionnaires via AJAX
                $.ajax({
                    url: "{{ route('api.questionnaires.by-period') }}",
                    type: "GET",
                    data: {
                        academic_period_id: academicPeriodId
                    },
                    success: function(response) {
                        let options = '<option value="">Pilih Kuesioner</option>';
                        response.forEach(function(questionnaire) {
                            options += `<option value="${questionnaire.id}">${questionnaire.title}</option>`;
                        });
                        $('#generate_questionnaire_id').html(options);
                    },
                    error: function(xhr) {
                        console.error('Error fetching questionnaires:', xhr);
                        alert('Terjadi kesalahan dalam mengambil data kuesioner.');
                    }
                });
            });

            // Export All Button
            $('#export-all-btn').click(function() {
                const academicPeriodId = $('#academic_period_id').val();
                const questionnaireType = $('#questionnaire_type').val();

                let url = "{{ route('reports.export-all') }}?";
                if (academicPeriodId) {
                    url += `academic_period_id=${academicPeriodId}&`;
                }
                if (questionnaireType) {
                    url += `questionnaire_type=${questionnaireType}`;
                }

                window.location.href = url;
            });
        });
    </script>
@endsection
