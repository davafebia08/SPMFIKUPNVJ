@extends('layouts.admin')

@section('title', 'Tambah Kuesioner - Admin SPM FIK UPNVJ')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Tambah Kuesioner Baru</h1>
                <p class="mb-0 text-muted">Tambahkan kuesioner baru ke sistem SPM</p>
            </div>

            <a href="{{ route('admin.questionnaires.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <!-- Form Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Kuesioner Baru</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.questionnaires.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="title" class="form-label">Nama Kuesioner<span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                                value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="type" class="form-label">Jenis Kuesioner<span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Pilih Jenis Kuesioner</option>
                                <option value="layanan_fakultas" {{ old('type') == 'layanan_fakultas' ? 'selected' : '' }}>Layanan Fakultas</option>
                                <option value="elom" {{ old('type') == 'elom' ? 'selected' : '' }}>Evaluasi Layanan oleh Mahasiswa (ELOM)</option>
                                <option value="evaluasi_dosen" {{ old('type') == 'evaluasi_dosen' ? 'selected' : '' }}>Evaluasi Dosen oleh Mahasiswa
                                </option>
                                <option value="elta" {{ old('type') == 'elta' ? 'selected' : '' }}>Evaluasi Layanan Tugas Akhir (ELTA)</option>
                                <option value="kepuasan_dosen" {{ old('type') == 'kepuasan_dosen' ? 'selected' : '' }}>Kepuasan Dosen</option>
                                <option value="kepuasan_tendik" {{ old('type') == 'kepuasan_tendik' ? 'selected' : '' }}>Kepuasan Tenaga Kependidikan
                                </option>
                                <option value="kepuasan_alumni" {{ old('type') == 'kepuasan_alumni' ? 'selected' : '' }}>Kepuasan Alumni</option>
                                <option value="kepuasan_pengguna_lulusan" {{ old('type') == 'kepuasan_pengguna_lulusan' ? 'selected' : '' }}>Kepuasan
                                    Pengguna Lulusan</option>
                                <option value="kepuasan_mitra" {{ old('type') == 'kepuasan_mitra' ? 'selected' : '' }}>Kepuasan Mitra Kerjasama</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="academic_period_id" class="form-label">Periode Akademik<span class="text-danger">*</span></label>
                            <select class="form-select @error('academic_period_id') is-invalid @enderror" id="academic_period_id"
                                name="academic_period_id" required>
                                <option value="">Pilih Periode Akademik</option>
                                @foreach ($academicPeriods as $period)
                                    <option value="{{ $period->id }}" {{ old('academic_period_id') == $period->id ? 'selected' : '' }}>
                                        {{ $period->name }} ({{ $period->semester }} {{ $period->year }})
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_period_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Tanggal Mulai<span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date"
                                value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="end_date" class="form-label">Tanggal Selesai<span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date"
                                value="{{ old('end_date') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktifkan Kuesioner</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Kelompok Responden<span class="text-danger">*</span></label>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="role_mahasiswa" name="respondent_roles[]"
                                                    value="mahasiswa" {{ in_array('mahasiswa', old('respondent_roles', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_mahasiswa">Mahasiswa</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="role_dosen" name="respondent_roles[]"
                                                    value="dosen" {{ in_array('dosen', old('respondent_roles', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_dosen">Dosen</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="role_tendik" name="respondent_roles[]"
                                                    value="tendik" {{ in_array('tendik', old('respondent_roles', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_tendik">Tenaga Kependidikan</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="role_alumni" name="respondent_roles[]"
                                                    value="alumni" {{ in_array('alumni', old('respondent_roles', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_alumni">Alumni</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="role_pengguna_lulusan" name="respondent_roles[]"
                                                    value="pengguna_lulusan"
                                                    {{ in_array('pengguna_lulusan', old('respondent_roles', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_pengguna_lulusan">Pengguna Lulusan</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="role_mitra" name="respondent_roles[]"
                                                    value="mitra" {{ in_array('mitra', old('respondent_roles', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_mitra">Mitra</label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('respondent_roles')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Template Selection -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label for="template_questionnaire_id" class="form-label">Gunakan Pertanyaan dari Kuesioner Sebelumnya</label>
                            <select class="form-select @error('template_questionnaire_id') is-invalid @enderror" id="template_questionnaire_id"
                                name="template_questionnaire_id">
                                <option value="">Tidak menggunakan template (buat pertanyaan dari awal)</option>
                                @foreach ($previousQuestionnaires as $prevQuestionnaire)
                                    <option value="{{ $prevQuestionnaire->id }}"
                                        {{ old('template_questionnaire_id') == $prevQuestionnaire->id ? 'selected' : '' }}
                                        data-type="{{ $prevQuestionnaire->type }}">
                                        {{ $prevQuestionnaire->title }} ({{ $prevQuestionnaire->academicPeriod->name }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i> Pilih kuesioner untuk menyalin semua pertanyaannya. Sangat berguna saat
                                pergantian semester.
                            </div>
                            @error('template_questionnaire_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan & Lanjutkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Type auto-selection based on respondent roles
            const typeSelect = document.getElementById('type');
            const roleMahasiswa = document.getElementById('role_mahasiswa');
            const roleDosen = document.getElementById('role_dosen');
            const roleTendik = document.getElementById('role_tendik');
            const roleAlumni = document.getElementById('role_alumni');
            const rolePenggunaLulusan = document.getElementById('role_pengguna_lulusan');
            const roleMitra = document.getElementById('role_mitra');
            const templateSelect = document.getElementById('template_questionnaire_id');

            // Update type suggestion based on role selection
            function updateTypeSuggestion() {
                if (typeSelect.value !== '') return; // Don't override if already selected

                if (roleMahasiswa.checked && !roleDosen.checked && !roleTendik.checked && !roleAlumni.checked && !rolePenggunaLulusan.checked && !
                    roleMitra.checked) {
                    typeSelect.value = 'elom'; // Only mahasiswa selected
                } else if (roleDosen.checked && !roleMahasiswa.checked && !roleTendik.checked && !roleAlumni.checked && !rolePenggunaLulusan
                    .checked && !roleMitra.checked) {
                    typeSelect.value = 'kepuasan_dosen'; // Only dosen selected
                } else if (roleTendik.checked && !roleMahasiswa.checked && !roleDosen.checked && !roleAlumni.checked && !rolePenggunaLulusan
                    .checked && !roleMitra.checked) {
                    typeSelect.value = 'kepuasan_tendik'; // Only tendik selected
                } else if (roleAlumni.checked && !roleMahasiswa.checked && !roleDosen.checked && !roleTendik.checked && !rolePenggunaLulusan
                    .checked && !roleMitra.checked) {
                    typeSelect.value = 'kepuasan_alumni'; // Only alumni selected
                } else if (rolePenggunaLulusan.checked && !roleMahasiswa.checked && !roleDosen.checked && !roleTendik.checked && !roleAlumni
                    .checked && !roleMitra.checked) {
                    typeSelect.value = 'kepuasan_pengguna_lulusan'; // Only pengguna lulusan selected
                } else if (roleMitra.checked && !roleMahasiswa.checked && !roleDosen.checked && !roleTendik.checked && !roleAlumni.checked && !
                    rolePenggunaLulusan.checked) {
                    typeSelect.value = 'kepuasan_mitra'; // Only mitra selected
                } else if (roleMahasiswa.checked && roleDosen.checked && roleTendik.checked && roleAlumni.checked) {
                    typeSelect.value = 'layanan_fakultas'; // Multiple stakeholders
                }
            }

            // Update roles and type based on template selection
            templateSelect.addEventListener('change', function() {
                if (this.value) {
                    const selectedOption = this.options[this.selectedIndex];
                    const templateType = selectedOption.getAttribute('data-type');

                    // Auto-select the same type as the template
                    if (templateType) {
                        typeSelect.value = templateType;
                    }
                }
            });

            // Add event listeners
            roleMahasiswa.addEventListener('change', updateTypeSuggestion);
            roleDosen.addEventListener('change', updateTypeSuggestion);
            roleTendik.addEventListener('change', updateTypeSuggestion);
            roleAlumni.addEventListener('change', updateTypeSuggestion);
            rolePenggunaLulusan.addEventListener('change', updateTypeSuggestion);
            roleMitra.addEventListener('change', updateTypeSuggestion);

            // Date validation
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            endDateInput.addEventListener('change', function() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                if (endDate <= startDate) {
                    endDateInput.setCustomValidity('Tanggal selesai harus setelah tanggal mulai');
                } else {
                    endDateInput.setCustomValidity('');
                }
            });

            startDateInput.addEventListener('change', function() {
                if (endDateInput.value) {
                    const startDate = new Date(startDateInput.value);
                    const endDate = new Date(endDateInput.value);

                    if (endDate <= startDate) {
                        endDateInput.setCustomValidity('Tanggal selesai harus setelah tanggal mulai');
                    } else {
                        endDateInput.setCustomValidity('');
                    }
                }
            });
        });
    </script>
@endsection
