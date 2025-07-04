@extends('layouts.admin')

@section('title', 'Kelola Jadwal Kuesioner - Admin SPM FIK UPNVJ')

@section('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Kelola Jadwal Kuesioner</h1>
                <p class="mb-0 text-muted">Menambahkan dan mengelola jadwal pelaksanaan kuesioner</p>
            </div>

            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPeriodModal">
                <i class="fas fa-plus-circle me-1"></i> Tambah Periode
            </button>
        </div>

        <!-- Academic Periods Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Periode Akademik</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="periodsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Periode</th>
                                <th>Semester</th>
                                <th>Tahun Akademik</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($academicPeriods as $index => $period)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $period->name }}</td>
                                    <td>{{ $period->semester }}</td>
                                    <td>{{ $period->year }}</td>
                                    <td>{{ date('d F Y', strtotime($period->start_date)) }}</td>
                                    <td>{{ date('d F Y', strtotime($period->end_date)) }}</td>
                                    <td>
                                        @if ($period->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if (!$period->is_active)
                                                <form action="{{ route('admin.schedules.set-active', $period) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success" title="Aktifkan">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#editPeriodModal{{ $period->id }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <form action="{{ route('admin.schedules.destroy', $period) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger btn-delete" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Edit Period Modal -->
                                        <div class="modal fade" id="editPeriodModal{{ $period->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Periode Akademik</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('admin.schedules.update', $period) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="name{{ $period->id }}" class="form-label">Nama Periode</label>
                                                                <input type="text" class="form-control" id="name{{ $period->id }}" name="name"
                                                                    value="{{ $period->name }}" required>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-6">
                                                                    <label for="semester{{ $period->id }}" class="form-label">Semester</label>
                                                                    <select class="form-select" id="semester{{ $period->id }}" name="semester"
                                                                        required>
                                                                        <option value="Ganjil" {{ $period->semester == 'Ganjil' ? 'selected' : '' }}>
                                                                            Ganjil</option>
                                                                        <option value="Genap" {{ $period->semester == 'Genap' ? 'selected' : '' }}>Genap
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="year{{ $period->id }}" class="form-label">Tahun Akademik</label>
                                                                    <input type="text" class="form-control" id="year{{ $period->id }}"
                                                                        name="year" value="{{ $period->year }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-6">
                                                                    <label for="start_date{{ $period->id }}" class="form-label">Tanggal Mulai</label>
                                                                    <input type="date" class="form-control" id="start_date{{ $period->id }}"
                                                                        name="start_date" value="{{ $period->start_date->format('Y-m-d') }}" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="end_date{{ $period->id }}" class="form-label">Tanggal Selesai</label>
                                                                    <input type="date" class="form-control" id="end_date{{ $period->id }}"
                                                                        name="end_date" value="{{ $period->end_date->format('Y-m-d') }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" id="is_active{{ $period->id }}"
                                                                    name="is_active" {{ $period->is_active ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="is_active{{ $period->id }}">Aktifkan
                                                                    Periode</label>
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
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-3">Belum ada periode akademik yang ditambahkan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Information Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Jadwal Kuesioner</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i> Penjelasan</h5>
                    <p>Jadwal kuesioner merupakan periode akademik dimana kuesioner dapat diakses oleh pengguna. Setiap kuesioner harus terkait dengan
                        satu periode akademik.</p>
                    <hr>
                    <p class="mb-0">Catatan penting:</p>
                    <ul class="mb-0">
                        <li>Hanya satu periode yang dapat aktif pada satu waktu.</li>
                        <li>Periode yang memiliki kuesioner terkait tidak dapat dihapus.</li>
                        <li>Periode aktif tidak dapat dihapus. Aktifkan periode lain terlebih dahulu sebelum menghapus.</li>
                        <li>Anda dapat menyalin kuesioner dari periode lain saat membuat periode baru.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Period Modal -->
    <div class="modal fade" id="addPeriodModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Periode Akademik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.schedules.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Periode</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                placeholder="Contoh: Semester Ganjil 2024/2025">
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="semester" class="form-label">Semester</label>
                                <select class="form-select" id="semester" name="semester" required>
                                    <option value="" selected disabled>Pilih Semester</option>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="year" class="form-label">Tahun Akademik</label>
                                <input type="text" class="form-control" id="year" name="year" required placeholder="Contoh: 2024/2025">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                        </div>

                        <!-- Copy Questionnaires Section -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="copy_questionnaires" value="0">
                                <input class="form-check-input" type="checkbox" id="copy_questionnaires" name="copy_questionnaires" value="1">
                                <label class="form-check-label" for="copy_questionnaires">Salin kuesioner dari periode lain</label>
                            </div>
                        </div>

                        <div class="mb-3" id="source_period_section" style="display: none;">
                            <label for="source_period_id" class="form-label">Pilih Periode Sumber</label>
                            <select class="form-select" id="source_period_id" name="source_period_id">
                                <option value="">Pilih periode...</option>
                                @foreach ($periodsWithQuestionnaires as $sourcePeriod)
                                    <option value="{{ $sourcePeriod->id }}">
                                        {{ $sourcePeriod->name }} ({{ $sourcePeriod->questionnaires_count }} kuesioner)
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Semua kuesioner dan pertanyaan akan disalin ke periode baru.</small>
                        </div>

                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1">
                            <label class="form-check-label" for="is_active">Aktifkan Periode</label>
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
@endsection

@section('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DataTable initialization - cek apakah jQuery sudah loaded
            if (typeof $ !== 'undefined') {
                $('#periodsTable').DataTable({
                    "language": {
                        "lengthMenu": "Tampilkan _MENU_ data per halaman",
                        "zeroRecords": "Data tidak ditemukan",
                        "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                        "infoEmpty": "Tidak ada data tersedia",
                        "infoFiltered": "(difilter dari _MAX_ total data)",
                        "search": "Cari:",
                        "paginate": {
                            "first": "Pertama",
                            "last": "Terakhir",
                            "next": "Selanjutnya",
                            "previous": "Sebelumnya"
                        }
                    },
                    "order": [
                        [3, "desc"],
                        [2, "desc"]
                    ]
                });
            }

            // Toggle source period dropdown
            const copyCheckbox = document.getElementById('copy_questionnaires');
            const sourcePeriodSection = document.getElementById('source_period_section');
            const sourcePeriodSelect = document.getElementById('source_period_id');

            copyCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    sourcePeriodSection.style.display = 'block';
                    sourcePeriodSelect.required = true;
                } else {
                    sourcePeriodSection.style.display = 'none';
                    sourcePeriodSelect.required = false;
                    sourcePeriodSelect.value = '';
                }
            });

            // Date validation for add modal
            const addStartDateInput = document.getElementById('start_date');
            const addEndDateInput = document.getElementById('end_date');

            function validateDates(startInput, endInput) {
                endInput.addEventListener('change', function() {
                    const startDate = new Date(startInput.value);
                    const endDate = new Date(endInput.value);

                    if (endDate <= startDate) {
                        endInput.setCustomValidity('Tanggal selesai harus setelah tanggal mulai');
                    } else {
                        endInput.setCustomValidity('');
                    }
                });

                startInput.addEventListener('change', function() {
                    if (endInput.value) {
                        const startDate = new Date(startInput.value);
                        const endDate = new Date(endInput.value);

                        if (endDate <= startDate) {
                            endInput.setCustomValidity('Tanggal selesai harus setelah tanggal mulai');
                        } else {
                            endInput.setCustomValidity('');
                        }
                    }
                });
            }

            validateDates(addStartDateInput, addEndDateInput);

            // Year input suggestion based on semester
            const semesterSelect = document.getElementById('semester');
            const yearInput = document.getElementById('year');
            const nameInput = document.getElementById('name');

            semesterSelect.addEventListener('change', function() {
                const currentYear = new Date().getFullYear();
                const semester = this.value;
                let academicYear = '';

                if (semester === 'Ganjil') {
                    academicYear = `${currentYear}/${currentYear + 1}`;
                } else if (semester === 'Genap') {
                    academicYear = `${currentYear - 1}/${currentYear}`;
                }

                if (!yearInput.value) {
                    yearInput.value = academicYear;
                }

                if (!nameInput.value) {
                    nameInput.value = `Semester ${semester} ${academicYear}`;
                }
            });

            // Date validation for edit modals
            @foreach ($academicPeriods as $period)
                const editStartDateInput{{ $period->id }} = document.getElementById('start_date{{ $period->id }}');
                const editEndDateInput{{ $period->id }} = document.getElementById('end_date{{ $period->id }}');
                validateDates(editStartDateInput{{ $period->id }}, editEndDateInput{{ $period->id }});
            @endforeach
        });
    </script>
@endsection
