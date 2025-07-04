@extends('layouts.admin')

@section('title', $questionnaire->title . ' - Detail Kuesioner')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Detail Kuesioner</h1>
                <p class="mb-0 text-muted">{{ $questionnaire->title }}</p>
            </div>

            <div>
                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#duplicateModal">
                    <i class="fas fa-copy me-1"></i> Duplikasi Kuesioner
                </button>
                <a href="{{ route('admin.questionnaires.index') }}" class="btn btn-outline-primary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ route('admin.questionnaires.edit', $questionnaire) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Edit Kuesioner
                </a>
            </div>
        </div>

        <!-- Questionnaire Info Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Kuesioner</h6>
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
                <span class="badge bg-{{ $statusClass }} py-2 px-3">{{ $statusText }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">Judul</th>
                                <td>{{ $questionnaire->title }}</td>
                            </tr>
                            <tr>
                                <th>Deskripsi</th>
                                <td>{{ $questionnaire->description ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Jenis</th>
                                <td>
                                    @php
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
                                    {{ $typeLabels[$questionnaire->type] ?? $questionnaire->type }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">Periode</th>
                                <td>{{ $questionnaire->academicPeriod->name }} ({{ $questionnaire->academicPeriod->semester }}
                                    {{ $questionnaire->academicPeriod->year }})</td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>{{ date('d F Y', strtotime($questionnaire->start_date)) }} -
                                    {{ date('d F Y', strtotime($questionnaire->end_date)) }}</td>
                            </tr>
                            <tr>
                                <th>Responden</th>
                                <td>
                                    @php
                                        $respondents = $questionnaire->permissions()->where('can_fill', true)->pluck('role')->toArray();

                                        $roleLabels = [
                                            'mahasiswa' => 'Mahasiswa',
                                            'dosen' => 'Dosen',
                                            'tendik' => 'Tenaga Kependidikan',
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
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions Management -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Pertanyaan</h6>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Pertanyaan
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="questionsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Aspek</th>
                                <th width="55%">Pertanyaan</th>
                                <th width="10%">Wajib</th>
                                <th width="10%">Status</th>
                                <th width="5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-questions">
                            @php
                                $questionsGrouped = $questionnaire->questions->sortBy('order')->groupBy('category.name');
                            @endphp

                            @forelse($questionsGrouped as $category => $questions)
                                @foreach ($questions as $index => $question)
                                    <tr data-question-id="{{ $question->id }}">
                                        <td>{{ $question->order }}</td>
                                        <td>{{ $category }}</td>
                                        <td>{{ $question->question }}</td>
                                        <td class="text-center">
                                            @if ($question->is_required)
                                                <span class="badge bg-success">Ya</span>
                                            @else
                                                <span class="badge bg-secondary">Tidak</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($question->is_active)
                                                <span class="badge bg-primary">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Non-Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-warning"
                                                    onclick="editQuestion({{ $question->id }}, '{{ $question->category_id }}', '{{ addslashes($question->question) }}', {{ $question->is_required ? 'true' : 'false' }}, {{ $question->is_active ? 'true' : 'false' }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger btn-delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3">Belum ada pertanyaan untuk kuesioner ini</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($questionnaire->questions->count() > 0)
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i> Anda dapat mengubah urutan pertanyaan dengan drag and drop pada tabel di atas.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Question Modal -->
    <div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addQuestionModalLabel">Tambah Pertanyaan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.questionnaires.questions.store', $questionnaire) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Aspek<span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Pilih Aspek</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="question" class="form-label">Pertanyaan<span class="text-danger">*</span></label>
                            <textarea class="form-control" id="question" name="question" rows="3" required></textarea>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_required" name="is_required" checked>
                            <label class="form-check-label" for="is_required">Wajib diisi</label>
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

    <!-- Edit Question Modal -->
    <div class="modal fade" id="editQuestionModal" tabindex="-1" aria-labelledby="editQuestionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editQuestionModalLabel">Edit Pertanyaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editQuestionForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_category_id" class="form-label">Aspek<span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_category_id" name="category_id" required>
                                <option value="">Pilih Aspek</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_question" class="form-label">Pertanyaan<span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_question" name="question" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_is_required" name="is_required">
                                <label class="form-check-label" for="edit_is_required">Wajib diisi</label>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                            <label class="form-check-label" for="edit_is_active">Aktif</label>
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

    <!-- Duplicate Questionnaire Modal -->
    <div class="modal fade" id="duplicateModal" tabindex="-1" aria-labelledby="duplicateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="duplicateModalLabel">Duplikasi Kuesioner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.questionnaires.duplicate', $questionnaire) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i> Fitur ini akan membuat salinan kuesioner dengan semua pertanyaan yang sama, tetapi
                            untuk periode baru.
                        </div>

                        <div class="mb-3">
                            <label for="academic_period_id" class="form-label">Periode Akademik<span class="text-danger">*</span></label>
                            <select class="form-select" id="academic_period_id" name="academic_period_id" required>
                                <option value="">Pilih Periode Akademik</option>
                                @foreach (\App\Models\AcademicPeriod::orderBy('year', 'desc')->orderBy('semester', 'desc')->get() as $period)
                                    <option value="{{ $period->id }}">
                                        {{ $period->name }} ({{ $period->semester }} {{ $period->year }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="dup_start_date" class="form-label">Tanggal Mulai<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="dup_start_date" name="start_date" required>
                            </div>

                            <div class="col-md-6">
                                <label for="dup_end_date" class="form-label">Tanggal Selesai<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="dup_end_date" name="end_date" required>
                            </div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="dup_is_active" name="is_active">
                            <label class="form-check-label" for="dup_is_active">Aktifkan kuesioner langsung</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-copy me-1"></i> Duplikasi Kuesioner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        // Edit Question
        function editQuestion(id, categoryId, question, isRequired, isActive) {
            const form = document.getElementById('editQuestionForm');
            form.action = `/admin/questions/${id}`;

            document.getElementById('edit_category_id').value = categoryId;
            document.getElementById('edit_question').value = question;
            document.getElementById('edit_is_required').checked = isRequired;
            document.getElementById('edit_is_active').checked = isActive;

            const modal = new bootstrap.Modal(document.getElementById('editQuestionModal'));
            modal.show();
        }

        // Sortable Questions
        $(function() {
            $("#sortable-questions").sortable({
                items: "tr",
                cursor: "move",
                opacity: 0.6,
                update: function() {
                    updateQuestionOrder();
                }
            });
        });

        function updateQuestionOrder() {
            const questions = [];
            $('#sortable-questions tr').each(function(index) {
                const questionId = $(this).data('question-id');
                if (questionId) {
                    questions.push(questionId);
                    $(this).find('td:first').text(index + 1);
                }
            });

            // Save new order via AJAX
            $.ajax({
                url: "{{ route('admin.questionnaires.questions.order', $questionnaire) }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    questions: questions
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Order updated successfully');
                    }
                },
                error: function(error) {
                    console.error('Error updating order', error);
                }
            });
        }

        // Date validation for duplication
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('dup_start_date');
            const endDateInput = document.getElementById('dup_end_date');

            endDateInput.addEventListener('change', function() {
                if (startDateInput.value) {
                    const startDate = new Date(startDateInput.value);
                    const endDate = new Date(endDateInput.value);

                    if (endDate <= startDate) {
                        endDateInput.setCustomValidity('Tanggal selesai harus setelah tanggal mulai');
                    } else {
                        endDateInput.setCustomValidity('');
                    }
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
