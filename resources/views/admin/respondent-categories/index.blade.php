@extends('layouts.admin')

@section('title', 'Kategori Responden - Admin SPM FIK UPNVJ')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Kategori Responden</h1>
                <p class="mb-0 text-muted">Kelola kategori responden dan akses kuesioner terkait</p>
            </div>
        </div>

        <!-- Categories Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Kategori Responden</h6>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="rolesTabs" role="tablist">
                    @foreach ($roles as $roleKey => $roleLabel)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ $roleKey }}-tab" data-bs-toggle="tab"
                                data-bs-target="#{{ $roleKey }}-content" type="button" role="tab" aria-controls="{{ $roleKey }}-content"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                {{ $roleLabel }}
                                <span class="badge bg-secondary ms-1">{{ $rolePermissions[$roleKey]['count'] }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content mt-4" id="rolesTabsContent">
                    @foreach ($roles as $roleKey => $roleLabel)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $roleKey }}-content" role="tabpanel"
                            aria-labelledby="{{ $roleKey }}-tab">

                            <form action="{{ route('admin.respondent-categories.update', $roleKey) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h5>Kuesioner yang Dapat Diisi oleh {{ $roleLabel }}</h5>
                                        <p class="text-muted">Pilih kuesioner yang dapat diakses oleh kategori responden ini</p>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                                        </button>
                                    </div>
                                </div>

                                @if ($questionnaires->isNotEmpty())
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="5%">
                                                        <div class="form-check">
                                                            <input class="form-check-input check-all" type="checkbox" id="check-all-{{ $roleKey }}"
                                                                data-role="{{ $roleKey }}">
                                                        </div>
                                                    </th>
                                                    <th>Nama Kuesioner</th>
                                                    <th>Jenis</th>
                                                    <th>Periode</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($questionnaires as $questionnaire)
                                                    @php
                                                        $isChecked = in_array($questionnaire->id, $rolePermissions[$roleKey]['permissions']);

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

                                                        $typeLabels = [
                                                            'layanan_fakultas' => 'Layanan Fakultas',
                                                            'elom' => 'Evaluasi Layanan oleh Mahasiswa (ELOM)',
                                                            'evaluasi_dosen' => 'Evaluasi Dosen oleh Mahasiswa',
                                                            'elta' => 'Evaluasi Layanan Tugas Akhir (ELTA)',
                                                            'kepuasan_dosen' => 'Kepuasan Dosen',
                                                            'kepuasan_tendik' => 'Kepuasan Tenaga Kependidikan',
                                                            'kepuasan_alumni' => 'Kepuasan Alumni',
                                                            'kepuasan_pengguna_lulusan' => 'Kepuasan Pengguna Lulusan',
                                                            'kepuasan_mitra' => 'Kepuasan Mitra Kerjasama',
                                                        ];
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input questionnaire-checkbox" type="checkbox"
                                                                    name="questionnaire_ids[]" value="{{ $questionnaire->id }}"
                                                                    data-role="{{ $roleKey }}" {{ $isChecked ? 'checked' : '' }}>
                                                            </div>
                                                        </td>
                                                        <td>{{ $questionnaire->title }}</td>
                                                        <td>{{ $typeLabels[$questionnaire->type] ?? $questionnaire->type }}</td>
                                                        <td>
                                                            {{ $questionnaire->academicPeriod->name }}
                                                            <br>
                                                            <small class="text-muted">
                                                                {{ date('d/m/Y', strtotime($questionnaire->start_date)) }} -
                                                                {{ date('d/m/Y', strtotime($questionnaire->end_date)) }}
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-1"></i> Belum ada kuesioner yang tersedia.
                                    </div>
                                @endif
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Explanation Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Kategori Responden</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i> Penjelasan</h5>
                    <p>Kategori responden menentukan kuesioner apa saja yang dapat diisi oleh pengguna berdasarkan peran mereka dalam sistem.</p>
                    <hr>
                    <ul class="mb-0">
                        <li><strong>Mahasiswa</strong> - Pengguna dengan peran mahasiswa aktif</li>
                        <li><strong>Dosen</strong> - Pengguna dengan peran dosen aktif</li>
                        <li><strong>Tenaga Kependidikan</strong> - Pengguna dengan peran tenaga kependidikan</li>
                        <li><strong>Alumni</strong> - Pengguna yang sudah lulus dan terdaftar sebagai alumni</li>
                        <li><strong>Pengguna Lulusan</strong> - Instansi/perusahaan yang mempekerjakan alumni</li>
                        <li><strong>Mitra</strong> - Institusi yang menjalin kerjasama dengan fakultas</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add "Check All" functionality
            document.querySelectorAll('.check-all').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const role = this.dataset.role;
                    const isChecked = this.checked;

                    document.querySelectorAll(`.questionnaire-checkbox[data-role="${role}"]`).forEach(function(qCheckbox) {
                        qCheckbox.checked = isChecked;
                    });
                });
            });

            // Update "Check All" state based on individual checkboxes
            document.querySelectorAll('.questionnaire-checkbox').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const role = this.dataset.role;
                    const checkAll = document.getElementById(`check-all-${role}`);
                    const checkboxes = document.querySelectorAll(`.questionnaire-checkbox[data-role="${role}"]`);
                    const allChecked = Array.from(checkboxes).every(cb => cb.checked);

                    checkAll.checked = allChecked;
                    checkAll.indeterminate = !allChecked && Array.from(checkboxes).some(cb => cb.checked);
                });
            });

            // Initialize "Check All" state
            document.querySelectorAll('.check-all').forEach(function(checkbox) {
                const role = checkbox.dataset.role;
                const checkboxes = document.querySelectorAll(`.questionnaire-checkbox[data-role="${role}"]`);
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);

                checkbox.checked = allChecked;
                checkbox.indeterminate = !allChecked && Array.from(checkboxes).some(cb => cb.checked);
            });
        });
    </script>
@endsection
