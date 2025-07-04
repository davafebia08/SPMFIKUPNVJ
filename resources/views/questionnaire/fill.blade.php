@extends('layouts.app')

@section('title', 'Isi Kuesioner - ' . $questionnaire->title)

@section('content')
    <div class="container py-5">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 fw-bold text-orange">
                    <i class="fas fa-edit me-2"></i> Isi Kuesioner
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $questionnaire->title }}</li>
                    </ol>
                </nav>
            </div>
            <div class="mt-3 mt-md-0">
                <span class="badge bg-orange-light text-orange px-3 py-2 border border-orange">
                    <i class="far fa-clock me-1"></i>
                    Batas waktu: {{ date('d M Y', strtotime($questionnaire->end_date)) }}
                </span>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card border-0 shadow-orange rounded-xl overflow-hidden">
            <!-- Card Header -->
            <div class="card-header bg-orange text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="h4 mb-0 fw-bold">{{ $questionnaire->title }}</h2>
                        <p class="mb-0 opacity-75">{{ $questionnaire->description }}</p>
                    </div>
                    <div class="badge bg-white text-orange">
                        {{ strtoupper($questionnaire->type) }}
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="card-body p-4">
                <!-- Instructions -->
                <div class="alert bg-orange-light border-orange text-orange rounded-xl">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle fs-4 mt-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="alert-heading fw-bold">Petunjuk Pengisian</h5>
                            <ul class="mb-0">
                                <li>Berikan penilaian sesuai pengalaman Anda menggunakan skala 1-4</li>
                                <li>Semua pertanyaan bertanda <span class="text-danger fw-bold">*</span> wajib diisi</li>
                                <li>Anda bisa memberikan komentar tambahan untuk setiap pertanyaan</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Rating Legend -->
                <div class="mb-4 p-3 bg-orange-light rounded-xl">
                    <div class="row g-3 text-center">
                        <div class="col-6 col-md-3">
                            <div class="p-2 bg-danger bg-opacity-10 rounded-xl">
                                <span class="badge bg-danger me-2">1</span>
                                <span>Kurang</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 bg-warning bg-opacity-10 rounded-xl">
                                <span class="badge bg-warning me-2">2</span>
                                <span>Cukup</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 bg-primary bg-opacity-10 rounded-xl">
                                <span class="badge bg-primary me-2">3</span>
                                <span>Baik</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 bg-success bg-opacity-10 rounded-xl">
                                <span class="badge bg-success me-2">4</span>
                                <span>Sangat Baik</span>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('responses.store', $questionnaire) }}" method="POST" id="questionnaireForm">
                    @csrf

                    @foreach ($questionsByCategory as $categoryName => $questions)
                        <div class="card mb-4 border-0 shadow-sm rounded-xl">
                            <div class="card-header bg-orange-light py-3">
                                <h3 class="h5 mb-0 fw-bold text-orange">
                                    {{ $categoryName }} .
                                    @if ($categoryName == '1')
                                        Reliability
                                    @elseif ($categoryName == '2')
                                        Responsiveness
                                    @elseif ($categoryName == '3')
                                        Assurance
                                    @elseif ($categoryName == '4')
                                        Empathy
                                    @else
                                        Tangible
                                    @endif

                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="50%" class="ps-4">Pertanyaan</th>
                                                <th width="40%" class="text-center">Penilaian</th>
                                                <th width="10%">Komentar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($questions as $question)
                                                <tr class="border-top">
                                                    <td class="ps-4">
                                                        <div class="d-flex">
                                                            @if ($question->is_required)
                                                                <span class="text-danger me-2">*</span>
                                                            @endif
                                                            <span>{{ $question->question }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="rating-buttons d-flex justify-content-center">
                                                            @for ($i = 1; $i <= 4; $i++)
                                                                <div class="form-check form-check-inline mx-1">
                                                                    <input class="form-check-input" type="radio" name="ratings[{{ $question->id }}]"
                                                                        id="rating_{{ $question->id }}_{{ $i }}" value="{{ $i }}"
                                                                        {{ old('ratings.' . $question->id) == $i ? 'checked' : '' }}
                                                                        {{ $question->is_required ? 'required' : '' }}>
                                                                    <label class="form-check-label rating-label rating-{{ $i }}"
                                                                        for="rating_{{ $question->id }}_{{ $i }}">
                                                                        {{ $i }}
                                                                    </label>
                                                                </div>
                                                            @endfor
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <!-- FIX: Tambahkan event.stopPropagation() dan ubah button type -->
                                                        <button type="button" class="btn btn-sm btn-outline-orange rounded-circle comment-btn"
                                                            onclick="openCommentModal(event, {{ $question->id }})" title="Tambahkan komentar">
                                                            <i class="far fa-comment"></i>
                                                        </button>
                                                        <!-- FIX: Tambahkan hidden textarea yang bisa di-submit -->
                                                        <input type="hidden" name="comments[{{ $question->id }}]"
                                                            id="hidden_comment_{{ $question->id }}" value="{{ old('comments.' . $question->id) }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Suggestion Section -->
                    <div class="card mb-4 border-0 shadow-sm rounded-xl">
                        <div class="card-header bg-orange-light py-3">
                            <h3 class="h5 mb-0 fw-bold text-orange">
                                <i class="fas fa-lightbulb me-2"></i>
                                Saran & Masukan
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-floating">
                                <textarea class="form-control rounded-xl" id="suggestion" name="suggestion" placeholder="Masukkan saran Anda" style="height: 100px">{{ old('suggestion') }}</textarea>
                                <label for="suggestion">Masukkan saran atau masukan untuk perbaikan (opsional)</label>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-orange px-4 rounded-xl">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-orange px-4 rounded-xl">
                            <i class="fas fa-paper-plane me-2"></i> Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- FIX: Pindahkan Modal ke luar container dan form dengan z-index tinggi -->
    @foreach ($questionsByCategory as $categoryName => $questions)
        @foreach ($questions as $question)
            <div class="modal fade" id="commentModal_{{ $question->id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
                data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-xl">
                        <div class="modal-header bg-orange text-white">
                            <h5 class="modal-title">Komentar untuk Pertanyaan</h5>
                            <button type="button" class="btn-close btn-close-white" onclick="closeCommentModal({{ $question->id }})"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-3 fw-semibold">{{ $question->question }}</p>
                            <div class="form-floating">
                                <textarea class="form-control rounded-xl" id="modal_comment_{{ $question->id }}" placeholder="Masukkan komentar" style="height: 100px">{{ old('comments.' . $question->id) }}</textarea>
                                <label for="modal_comment_{{ $question->id }}">Masukkan komentar (opsional)</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-orange rounded-xl" onclick="closeCommentModal({{ $question->id }})">
                                Tutup
                            </button>
                            <button type="button" class="btn btn-orange rounded-xl" onclick="saveComment({{ $question->id }})">
                                Simpan Komentar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach
@endsection

@section('styles')
    <style>
        .rating-buttons .form-check-input {
            position: absolute;
            opacity: 0;
        }

        .rating-buttons .rating-label {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 600;
        }

        .rating-buttons .form-check-input:checked+.rating-label {
            color: white;
            transform: scale(1.1);
        }

        .rating-4 {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .rating-3 {
            background-color: rgba(255, 107, 53, 0.1);
            color: var(--primary-color);
        }

        .rating-2 {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .rating-1 {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .rating-buttons .form-check-input:checked+.rating-4 {
            background-color: #198754;
        }

        .rating-buttons .form-check-input:checked+.rating-3 {
            background-color: var(--primary-color);
        }

        .rating-buttons .form-check-input:checked+.rating-2 {
            background-color: #ffc107;
        }

        .rating-buttons .form-check-input:checked+.rating-1 {
            background-color: #dc3545;
        }

        .comment-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(255, 107, 53, 0.03);
        }

        .btn-outline-orange {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-orange:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-orange {
            background-color: var(--primary-color);
            color: white;
            border: none;
            transition: all 0.3s;
        }

        .btn-orange:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 107, 53, 0.3);
        }

        /* FIX: Modal z-index untuk mengatasi kedip-kedip */
        .modal {
            z-index: 9999 !important;
        }

        .modal-backdrop {
            z-index: 9998 !important;
        }

        /* FIX: Mencegah event bubbling */
        .comment-btn {
            z-index: 1;
            position: relative;
        }
    </style>
@endsection

@section('scripts')
    <script>
        // FIX: JavaScript functions untuk mengatasi modal kedip-kedip
        let currentModalId = null;

        function openCommentModal(event, questionId) {
            // Stop event bubbling
            event.preventDefault();
            event.stopPropagation();

            currentModalId = questionId;

            // Ambil nilai dari hidden input jika ada
            const hiddenInput = document.getElementById(`hidden_comment_${questionId}`);
            const modalTextarea = document.getElementById(`modal_comment_${questionId}`);

            if (hiddenInput && modalTextarea) {
                modalTextarea.value = hiddenInput.value;
            }

            // Buka modal menggunakan Bootstrap JavaScript API
            const modal = new bootstrap.Modal(document.getElementById(`commentModal_${questionId}`), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
        }

        function closeCommentModal(questionId) {
            const modal = bootstrap.Modal.getInstance(document.getElementById(`commentModal_${questionId}`));
            if (modal) {
                modal.hide();
            }
            currentModalId = null;
        }

        function saveComment(questionId) {
            // Ambil nilai dari textarea modal
            const modalTextarea = document.getElementById(`modal_comment_${questionId}`);
            const hiddenInput = document.getElementById(`hidden_comment_${questionId}`);

            if (modalTextarea && hiddenInput) {
                hiddenInput.value = modalTextarea.value;

                // Update tampilan button jika ada komentar
                const commentBtn = document.querySelector(`[onclick="openCommentModal(event, ${questionId})"]`);
                if (modalTextarea.value.trim()) {
                    commentBtn.classList.remove('btn-outline-orange');
                    commentBtn.classList.add('btn-orange');
                    commentBtn.innerHTML = '<i class="fas fa-comment"></i>';
                } else {
                    commentBtn.classList.remove('btn-orange');
                    commentBtn.classList.add('btn-outline-orange');
                    commentBtn.innerHTML = '<i class="far fa-comment"></i>';
                }
            }

            // Tutup modal
            closeCommentModal(questionId);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Highlight table row when rating is selected
            document.querySelectorAll('.form-check-input').forEach(input => {
                input.addEventListener('change', function() {
                    const row = this.closest('tr');
                    if (this.checked) {
                        row.classList.add('table-active');
                    }
                });

                // Initialize checked state
                if (input.checked) {
                    input.closest('tr').classList.add('table-active');
                }
            });

            // Initialize comment button states
            document.querySelectorAll('input[type="hidden"][name^="comments"]').forEach(hiddenInput => {
                if (hiddenInput.value.trim()) {
                    const questionId = hiddenInput.id.replace('hidden_comment_', '');
                    const commentBtn = document.querySelector(`[onclick="openCommentModal(event, ${questionId})"]`);
                    if (commentBtn) {
                        commentBtn.classList.remove('btn-outline-orange');
                        commentBtn.classList.add('btn-orange');
                        commentBtn.innerHTML = '<i class="fas fa-comment"></i>';
                    }
                }
            });

            // Show confirmation before submitting
            document.getElementById('questionnaireForm').addEventListener('submit', function(e) {
                // Debug: Log semua data form sebelum submit
                const formData = new FormData(this);
                console.log('Form data being submitted:');
                for (let [key, value] of formData.entries()) {
                    console.log(key + ': ' + value);
                }

                // Tutup modal yang masih terbuka
                if (currentModalId) {
                    closeCommentModal(currentModalId);
                }

                // Kumpulkan semua nama pertanyaan yang required
                const requiredQuestionNames = new Set();
                document.querySelectorAll('input[required]').forEach(input => {
                    requiredQuestionNames.add(input.name);
                });

                // Periksa untuk setiap kelompok pertanyaan apakah sudah ada jawaban
                const unansweredQuestions = [];
                requiredQuestionNames.forEach(name => {
                    if (!document.querySelector(`input[name="${name}"]:checked`)) {
                        unansweredQuestions.push(name);
                    }
                });

                if (unansweredQuestions.length > 0) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            html: `Ada <strong>${unansweredQuestions.length}</strong> pertanyaan wajib yang belum diisi. Silakan lengkapi terlebih dahulu.`,
                            confirmButtonText: 'Mengerti'
                        });
                    } else {
                        alert(`Ada ${unansweredQuestions.length} pertanyaan wajib yang belum diisi.`);
                    }
                } else {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Kirim Jawaban?',
                            text: "Anda tidak akan bisa mengubah jawaban setelah dikirim",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#FF6B35',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Kirim Sekarang!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById('questionnaireForm').submit();
                            }
                        });
                    } else {
                        if (confirm('Kirim jawaban sekarang?')) {
                            document.getElementById('questionnaireForm').submit();
                        }
                    }
                }
            });
        });
    </script>
@endsection
