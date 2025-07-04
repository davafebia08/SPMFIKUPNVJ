@extends('layouts.app')

@section('title', 'Manajemen Periode Akademik')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Manajemen Periode Akademik</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPeriodModal">
                <i class="fas fa-plus"></i> Tambah Periode Akademik
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Daftar Periode Akademik
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Nama</th>
                            <th width="15%">Semester</th>
                            <th width="15%">Tahun Akademik</th>
                            <th width="15%">Tanggal Mulai</th>
                            <th width="15%">Tanggal Selesai</th>
                            <th width="5%">Status</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($academicPeriods as $index => $period)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $period->name }}</td>
                                <td>{{ $period->semester }}</td>
                                <td>{{ $period->year }}</td>
                                <td>{{ $period->start_date->format('d/m/Y') }}</td>
                                <td>{{ $period->end_date->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge {{ $period->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $period->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-warning edit-period" data-id="{{ $period->id }}"
                                            data-name="{{ $period->name }}" data-semester="{{ $period->semester }}" data-year="{{ $period->year }}"
                                            data-start="{{ $period->start_date->format('Y-m-d') }}" data-end="{{ $period->end_date->format('Y-m-d') }}"
                                            data-active="{{ $period->is_active }}" data-bs-toggle="modal" data-bs-target="#editPeriodModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if (!$period->is_active)
                                            <form action="{{ route('academic-periods.set-active', $period) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-success" title="Set as Active">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deletePeriodModal{{ $period->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deletePeriodModal{{ $period->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Apakah Anda yakin ingin menghapus periode akademik "{{ $period->name }}"?</p>
                                                    <p class="text-danger">Tindakan ini akan menghapus semua data terkait periode ini dan tidak dapat
                                                        dibatalkan.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <form action="{{ route('academic-periods.destroy', $period) }}" method="POST">
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
                                <td colspan="8" class="text-center">Tidak ada data periode akademik.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Period Modal -->
    <div class="modal fade" id="createPeriodModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('academic-periods.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Periode Akademik</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Periode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Contoh: Semester Ganjil 2024/2025"
                                required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                                <select class="form-select" id="semester" name="semester" required>
                                    <option value="">Pilih Semester</option>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="year" class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="year" name="year" placeholder="Contoh: 2024/2025" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>

                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1">
                                <label class="form-check-label" for="is_active">Set sebagai Periode Aktif</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Period Modal -->
    <div class="modal fade" id="editPeriodModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('academic-periods.update', 0) }}" method="POST" id="editPeriodForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Periode Akademik</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nama Periode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_semester" class="form-label">Semester <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_semester" name="semester" required>
                                    <option value="">Pilih Semester</option>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_year" class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_year" name="year" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_end_date" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_end_date" name="end_date" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                                <label class="form-check-label" for="edit_is_active">Set sebagai Periode Aktif</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Edit period
            $('.edit-period').click(function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const semester = $(this).data('semester');
                const year = $(this).data('year');
                const startDate = $(this).data('start');
                const endDate = $(this).data('end');
                const isActive = $(this).data('active');

                // Set form action URL
                $('#editPeriodForm').attr('action', "{{ url('academic-periods') }}/" + id);

                // Fill form fields
                $('#edit_name').val(name);
                $('#edit_semester').val(semester);
                $('#edit_year').val(year);
                $('#edit_start_date').val(startDate);
                $('#edit_end_date').val(endDate);
                $('#edit_is_active').prop('checked', isActive == 1);
            });

            // Validasi tanggal
            $('#end_date, #edit_end_date').change(function() {
                const startDateId = $(this).attr('id') === 'end_date' ? 'start_date' : 'edit_start_date';
                const startDate = $('#' + startDateId).val();
                const endDate = $(this).val();

                if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
                    alert('Tanggal selesai tidak boleh lebih awal dari tanggal mulai!');
                    $(this).val('');
                }
            });
        });
    </script>
@endsection
