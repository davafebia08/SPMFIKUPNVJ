@extends('layouts.admin')

@section('title', 'Kelola Kuesioner - Admin SPM FIK UPNVJ')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Kelola Kuesioner</h1>
                <p class="mb-0 text-muted">Tambah, edit, dan kelola kuesioner SPM FIK UPNVJ</p>
            </div>

            <div class="d-flex">
                <!-- Filter Period -->
                <form action="{{ route('admin.questionnaires.index') }}" method="GET" class="d-flex me-2">
                    <select name="academic_period_id" class="form-select me-2" onchange="this.form.submit()">
                        @foreach ($academicPeriods as $period)
                            <option value="{{ $period->id }}" {{ $selectedPeriodId == $period->id ? 'selected' : '' }}>
                                {{ $period->name }} ({{ $period->semester }} {{ $period->year }})
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </form>

                <!-- Add Button -->
                <a href="{{ route('admin.questionnaires.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Kuesioner
                </a>
            </div>
        </div>

        <!-- Questionnaires Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Kuesioner</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="questionnairesTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="25%">Nama Kuesioner</th>
                                <th width="10%">Semester</th>
                                <th width="10%">Tahun</th>
                                <th width="15%">Periode</th>
                                <th width="10%">Status</th>
                                <th width="15%">Kelompok Responden</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($questionnaires as $index => $questionnaire)
                                <tr>
                                    <td>{{ $questionnaire->id }}</td>
                                    <td>{{ $questionnaire->title }}</td>
                                    <td>{{ $questionnaire->academicPeriod->semester }}</td>
                                    <td>{{ $questionnaire->academicPeriod->year }}</td>
                                    <td>
                                        {{ date('d/m/Y', strtotime($questionnaire->start_date)) }} -
                                        {{ date('d/m/Y', strtotime($questionnaire->end_date)) }}
                                    </td>
                                    <td>
                                        @php
                                            $now = \Carbon\Carbon::now();
                                            $startDate = \Carbon\Carbon::parse($questionnaire->start_date);
                                            $endDate = \Carbon\Carbon::parse($questionnaire->end_date);

                                            if (!$questionnaire->is_active) {
                                                $statusClass = 'secondary';
                                                $statusText = 'Non-Aktif';
                                            } elseif ($now->lt($startDate)) {
                                                $statusClass = 'info';
                                                $statusText = 'Akan Datang';
                                            } elseif ($now->gt($endDate)) {
                                                $statusClass = 'success';
                                                $statusText = 'Selesai';
                                            } else {
                                                $statusClass = 'primary';
                                                $statusText = 'Aktif';
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $respondents = $questionnaire->permissions()->where('can_fill', true)->pluck('role')->toArray();

                                            $roleLabels = [
                                                'mahasiswa' => 'Mahasiswa',
                                                'dosen' => 'Dosen',
                                                'tendik' => 'Tendik',
                                                'alumni' => 'Alumni',
                                                'pengguna_lulusan' => 'Pengguna Lulusan',
                                                'mitra' => 'Mitra',
                                            ];

                                            $formattedRoles = array_map(function ($role) use ($roleLabels) {
                                                return $roleLabels[$role] ?? $role;
                                            }, $respondents);
                                        @endphp

                                        @foreach ($formattedRoles as $role)
                                            <span class="badge bg-info me-1">{{ $role }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.questionnaires.show', $questionnaire) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.questionnaires.edit', $questionnaire) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.questionnaires.destroy', $questionnaire) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger btn-delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-3">Tidak ada kuesioner untuk periode yang dipilih</td>
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
            $('#questionnairesTable').DataTable({
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
                }
            });
        });
    </script>
@endsection
