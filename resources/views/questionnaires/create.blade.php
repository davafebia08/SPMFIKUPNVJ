@extends('layouts.app')

@section('title', 'Buat Kuesioner Baru')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Buat Kuesioner Baru</h1>
    </div>

    <form action="{{ route('questionnaires.store') }}" method="POST">
        @csrf

        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Informasi Kuesioner
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Kuesioner <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                                value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="type" class="form-label">Jenis Kuesioner <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Pilih Jenis Kuesioner</option>
                                    @foreach ($questionnaireTypes as $value => $label)
                                        <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="academic_period_id" class="form-label">Periode Akademik <span class="text-danger">*</span></label>
                                <select class="form-select @error('academic_period_id') is-invalid @enderror" id="academic_period_id"
                                    name="academic_period_id" required>
                                    <option value="">Pilih Periode Akademik</option>
                                    @foreach ($academicPeriods as $period)
                                        <option value="{{ $period->id }}" {{ old('academic_period_id') == $period->id ? 'selected' : '' }}>
                                            {{ $period->name }}
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
                                <label for="start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date"
                                    value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date"
                                    value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="roles" class="form-label">Kategori Responden <span class="text-danger">*</span></label>
                            <select class="form-select select2 @error('roles') is-invalid @enderror" id="roles" name="roles[]" multiple required>
                                @foreach ($roles as $value => $label)
                                    <option value="{{ $value }}" {{ old('roles') && in_array($value, old('roles')) ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Pilih kategori responden yang dapat mengisi kuesioner ini.</div>
                            @error('roles')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                    {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktifkan Kuesioner</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Petunjuk
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-info-circle text-info me-2"></i> Tambahkan pertanyaan dengan mengklik tombol di bawah.</li>
                            <li><i class="fas fa-info-circle text-info me-2"></i> Setiap pertanyaan harus terkait dengan salah satu kategori.</li>
                            <li><i class="fas fa-info-circle text-info me-2"></i> Pertanyaan dapat diurutkan dengan menyeret baris tabel.</li>
                            <li><i class="fas fa-info-circle text-info me-2"></i> Skala penilaian: 4 (Sangat Baik) hingga 1 (Kurang).</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Pertanyaan Kuesioner</span>
                <button type="button" class="btn btn-primary btn-sm" id="add-question">
                    <i class="fas fa-plus"></i> Tambah Pertanyaan
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="questions-table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="50%">Pertanyaan</th>
                                <th width="25%">Kategori</th>
                                <th width="10%">Wajib</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="questions-container">
                            @if (old('questions'))
                                @foreach (old('questions') as $index => $question)
                                    <tr class="question-row" data-index="{{ $index }}">
                                        <td class="question-number">{{ $index + 1 }}</td>
                                        <td>
                                            <input type="hidden" name="questions[{{ $index }}][order]" value="{{ $index }}">
                                            <textarea class="form-control @error('questions.' . $index . '.question') is-invalid @enderror" name="questions[{{ $index }}][question]"
                                                rows="2" required>{{ $question['question'] }}</textarea>
                                            @error('questions.' . $index . '.question')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <select class="form-select @error('questions.' . $index . '.category_id') is-invalid @enderror"
                                                name="questions[{{ $index }}][category_id]" required>
                                                <option value="">Pilih Kategori</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ $question['category_id'] == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('questions.' . $index . '.category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="questions[{{ $index }}][is_required]"
                                                    value="1"
                                                    {{ !isset($question['is_required']) || $question['is_required'] == '1' ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-question">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <div id="no-questions-message" class="{{ old('questions') && count(old('questions')) > 0 ? 'd-none' : '' }}">
                    <p class="text-center">Belum ada pertanyaan. Klik tombol "Tambah Pertanyaan" untuk menambahkan.</p>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
            <a href="{{ route('questionnaires.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Kuesioner</button>
        </div>
    </form>

    <!-- Template for new question row -->
    <template id="question-template">
        <tr class="question-row" data-index="{index}">
            <td class="question-number">{number}</td>
            <td>
                <input type="hidden" name="questions[{index}][order]" value="{index}">
                <textarea class="form-control" name="questions[{index}][question]" rows="2" required></textarea>
            </td>
            <td>
                <select class="form-select" name="questions[{index}][category_id]" required>
                    <option value="">Pilih Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </td>
            <td class="text-center">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="questions[{index}][is_required]" value="1" checked>
                </div>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-question">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            $('.select2').select2({
                theme: 'bootstrap-5'
            });

            // Sortable untuk tabel pertanyaan
            new Sortable(document.getElementById('questions-container'), {
                animation: 150,
                handle: '.question-number',
                onEnd: function() {
                    // Update nomor urut dan order setelah drag & drop
                    updateQuestionNumbers();
                }
            });

            // Variable untuk menghitung jumlah pertanyaan
            let questionIndex = {{ old('questions') ? count(old('questions')) : 0 }};

            // Tambah pertanyaan baru
            $('#add-question').click(function() {
                const template = $('#question-template').html();
                const newRow = template
                    .replace(/{index}/g, questionIndex)
                    .replace(/{number}/g, questionIndex + 1);

                $('#questions-container').append(newRow);
                questionIndex++;

                // Sembunyikan pesan "belum ada pertanyaan"
                $('#no-questions-message').addClass('d-none');
            });

            // Hapus pertanyaan
            $(document).on('click', '.remove-question', function() {
                $(this).closest('tr').remove();

                // Update nomor urut
                updateQuestionNumbers();

                // Tampilkan pesan jika tidak ada pertanyaan
                if ($('#questions-container tr').length === 0) {
                    $('#no-questions-message').removeClass('d-none');
                }
            });

            // Fungsi untuk memperbarui nomor urut dan order setelah drag & drop atau hapus
            function updateQuestionNumbers() {
                $('#questions-container tr').each(function(index) {
                    $(this).attr('data-index', index);
                    $(this).find('.question-number').text(index + 1);

                    // Update name attributes
                    $(this).find('textarea, select, input').each(function() {
                        const name = $(this).attr('name');
                        if (name) {
                            const newName = name.replace(/questions\[\d+\]/, `questions[${index}]`);
                            $(this).attr('name', newName);
                        }
                    });

                    // Update order value
                    $(this).find('input[name^="questions"][name$="[order]"]').val(index);
                });
            }

            // Validasi tanggal
            $('#end_date').change(function() {
                const startDate = $('#start_date').val();
                const endDate = $(this).val();

                if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
                    alert('Tanggal selesai tidak boleh lebih awal dari tanggal mulai!');
                    $(this).val('');
                }
            });

            // Tambahkan pertanyaan otomatis jika belum ada
            if (questionIndex === 0) {
                $('#add-question').click();
            }
        });
    </script>
@endsection
