@extends('layouts.app')

@section('title', $questionnaire->title . ' - SPM FIK UPNVJ')

@section('content')
    <div class="container py-5">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 fw-bold text-orange">
                    <i class="fas fa-clipboard-list me-2"></i> {{ $questionnaire->title }}
                </h1>
                <p class="text-muted mb-0">
                    Fakultas Ilmu Komputer, UPN "Veteran" Jakarta
                </p>
            </div>
            <div class="mt-3 mt-md-0">
                <span class="badge bg-orange text-white px-3 py-2 rounded-xl">
                    <i class="far fa-clock me-1"></i>
                    Periode: {{ $questionnaire->academicPeriod->name }}
                </span>
            </div>
        </div>

        <!-- Data Responden -->
        <div class="card border-0 shadow-sm rounded-xl mb-4">
            <div class="card-header bg-orange-light py-3">
                <h5 class="text-orange mb-0 fw-bold">Data Responden</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th width="40%">Nama</th>
                                <td width="60%">: {{ $responden['name'] }}</td>
                            </tr>
                            <tr>
                                <th>Jabatan</th>
                                <td>: {{ $responden['jabatan'] }}</td>
                            </tr>
                            <tr>
                                <th>Instansi</th>
                                <td>: {{ $responden['nama_instansi'] }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        @if ($questionnaire->type === 'kepuasan_pengguna_lulusan')
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th width="40%">Alumni</th>
                                    <td width="60%">: {{ $responden['nama_alumni'] }}</td>
                                </tr>
                                <tr>
                                    <th>Program Studi</th>
                                    <td>: {{ $responden['program_studi_alumni'] }}</td>
                                </tr>
                                <tr>
                                    <th>Tahun Lulus</th>
                                    <td>: {{ $responden['tahun_lulus_alumni'] }}</td>
                                </tr>
                            </table>
                        @elseif ($questionnaire->type === 'kepuasan_mitra')
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th width="40%">Jenis Mitra</th>
                                    <td width="60%">: {{ $responden['jenis_mitra'] }}</td>
                                </tr>
                                <tr>
                                    <th>Jenis Kerjasama</th>
                                    <td>: {{ $responden['jenis_kerjasama'] }}</td>
                                </tr>
                                <tr>
                                    <th>Periode Kerjasama</th>
                                    <td>: {{ $responden['periode_kerjasama'] }}</td>
                                </tr>
                                <tr>
                                    <th>Lingkup Kerjasama</th>
                                    <td>: {{ $responden['lingkup_kerjasama'] }}</td>
                                </tr>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card border-0 shadow-orange rounded-xl overflow-hidden">
            <!-- Card Header -->
            <div class="card-header bg-orange text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="h4 mb-0 fw-bold">Formulir Kuesioner</h2>
                        <p class="mb-0 opacity-75">Silakan lengkapi kuesioner berikut dengan penilaian Anda</p>
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
                            <p>Berikan penilaian terhadap pernyataan berikut sesuai dengan pengalaman Anda dengan nilai:</p>
                            <div class="row">
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="px-3 py-2 rounded bg-danger text-white text-center">
                                        <strong>1</strong> - Kurang
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="px-3 py-2 rounded bg-warning text-dark text-center">
                                        <strong>2</strong> - Cukup
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="px-3 py-2 rounded bg-primary text-white text-center">
                                        <strong>3</strong> - Baik
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="px-3 py-2 rounded bg-success text-white text-center">
                                        <strong>4</strong> - Sangat Baik
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($questionnaire->type === 'kepuasan_pengguna_lulusan')
                    <form action="{{ route('public.questionnaire.pengguna-lulusan.submit', $questionnaire) }}" method="POST">
                    @elseif ($questionnaire->type === 'kepuasan_mitra')
                        <form action="{{ route('public.questionnaire.mitra.submit', $questionnaire) }}" method="POST">
                        @else
                            <form action="#" method="POST">
                @endif
                @csrf

                <input type="hidden" name="form_step" value="kuesioner">

                <!-- Hidden inputs for responden data -->
                @foreach ($responden as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach

                <!-- Questions by Category -->
                @php
                    $questionsByCategory = $questions->groupBy(function ($question) {
                        return $question->category->name ?? 'Lainnya';
                    });
                @endphp

                @php
                    $allQuestions = [];
                @endphp

                @foreach ($questionsByCategory as $category => $questions)
                    <div class="mb-4">
                        <h4 class="border-bottom pb-2 mb-3 text-orange">{{ $category }}</h4>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="55%">Pernyataan</th>
                                        <th width="30%" class="text-center">Penilaian</th>
                                        <th width="10%" class="text-center">Komentar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($questions as $index => $question)
                                        @php $allQuestions[] = $question; @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $question->question }}</td>
                                            <td>
                                                <div class="d-flex justify-content-between rating-buttons">
                                                    @for ($i = 1; $i <= 4; $i++)
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="ratings[{{ $question->id }}]"
                                                                id="rating{{ $question->id }}_{{ $i }}" value="{{ $i }}"
                                                                {{ old('ratings.' . $question->id) == $i ? 'checked' : '' }}
                                                                {{ $question->is_required ? 'required' : '' }}>
                                                            <label class="form-check-label" for="rating{{ $question->id }}_{{ $i }}">
                                                                {{ $i }}
                                                            </label>
                                                        </div>
                                                    @endfor
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                                    data-bs-target="#commentModal{{ $question->id }}">
                                                    <i class="fas fa-comment"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach



                {{-- untuk mitra terdapat 2 kolom yaiut Saran dari mitra dan saran kemajuan FIK UPNVJ --}}
                @if ($questionnaire->type === 'kepuasan_mitra')
                    <div class="mb-4">
                        <h4 class="border-bottom pb-2 mb-3 text-orange">Saran dari Mitra</h4>
                        <div class="form-floating">
                            <textarea class="form-control rounded-xl" id="mitra_suggestion" name="mitra_suggestion" style="height: 150px">{{ old('mitra_suggestion') }}</textarea>
                            <label for="mitra_suggestion">Silahkan berikan saran atau masukan untuk kemajuan FIK UPNVJ (opsional)</label>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h4 class="border-bottom pb-2 mb-3 text-orange">Saran Kemajuan FIK UPNVJ</h4>
                        <div class="form-floating">
                            <textarea class="form-control rounded-xl" id="fik_suggestion" name="fik_suggestion" style="height: 150px">{{ old('fik_suggestion') }}</textarea>
                            <label for="fik_suggestion">Silahkan berikan saran atau masukan untuk kemajuan FIK UPNVJ (opsional)</label>
                        </div>
                    </div>
                @elseif ($questionnaire->type === 'kepuasan_pengguna_lulusan')
                    <div class="mb-4">
                        <h4 class="border-bottom pb-2 mb-3 text-orange">Bagaimana harapan Anda terhadap lulusan FIK UPNVJ?</h4>
                        <div class="form-floating">
                            <textarea class="form-control rounded-xl" id="expectation" name="expectation" style="height: 150px">{{ old('expectation') }}</textarea>
                            <label for="expectation">Silahkan berikan harapan Anda terhadap lulusan FIK UPNVJ (opsional)</label>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h4 class="border-bottom pb-2 mb-3 text-orange">Saran dan masukan untuk FIK UPNVJ?</h4>
                        <div class="form-floating">
                            <textarea class="form-control rounded-xl" id="suggestion" name="suggestion" style="height: 150px">{{ old('suggestion') }}</textarea>
                            <label for="suggestion">Silahkan berikan saran atau masukan untuk perbaikan pelayanan (opsional)</label>
                        </div>
                    </div>
                @else
                    <!-- Saran dan Masukan -->
                    <div class="mb-4">
                        <h4 class="border-bottom pb-2 mb-3 text-orange">Saran dan Masukan</h4>
                        <div class="form-floating">
                            <textarea class="form-control rounded-xl" id="suggestion" name="suggestion" style="height: 150px">{{ old('suggestion') }}</textarea>
                            <label for="suggestion">Silahkan berikan saran atau masukan untuk perbaikan pelayanan (opsional)</label>
                        </div>
                    </div>
                @endif


                <div class="d-flex justify-content-between mt-4">
                    @if ($questionnaire->type === 'kepuasan_pengguna_lulusan')
                        <a href="{{ route('public.questionnaire.pengguna-lulusan') }}" class="btn btn-outline-primary px-4 rounded-xl">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                    @elseif ($questionnaire->type === 'kepuasan_mitra')
                        <a href="{{ route('public.questionnaire.mitra') }}" class="btn btn-outline-primary px-4 rounded-xl">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                    @else
                        <a href="{{ route('public.questionnaire.index') }}" class="btn btn-outline-primary px-4 rounded-xl">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                    @endif
                    <button type="submit" class="btn btn-primary px-4 rounded-xl">
                        <i class="fas fa-paper-plane me-2"></i> Submit
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Comment Modals --}}
    @foreach ($allQuestions as $question)
        <div class="modal fade" id="commentModal{{ $question->id }}" tabindex="-1" aria-labelledby="commentModalLabel{{ $question->id }}"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-orange text-white">
                        <h5 class="modal-title" id="commentModalLabel{{ $question->id }}">
                            Tambahkan Komentar
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ $question->question }}</p>
                        <div class="form-floating">
                            <textarea class="form-control" id="comment{{ $question->id }}" name="comments[{{ $question->id }}]" style="height: 100px">{{ old('comments.' . $question->id) }}</textarea>
                            <label for="comment{{ $question->id }}">Komentar (opsional)</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Tutup
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('styles')
    <style>
        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(255, 107, 53, 0.25);
        }

        .rating-buttons {
            gap: 10px;
        }

        .rating-buttons .form-check {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .rating-buttons .form-check-input {
            margin: 0 auto 5px;
        }

        .rating-buttons label {
            cursor: pointer;
            font-weight: 500;
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validasi form sebelum submit
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                let valid = true;
                const requiredRadios = document.querySelectorAll('input[type=radio][required]');

                // Pengelompokan berdasarkan nama
                const radioGroups = {};
                requiredRadios.forEach(radio => {
                    if (!radioGroups[radio.name]) {
                        radioGroups[radio.name] = [];
                    }
                    radioGroups[radio.name].push(radio);
                });

                // Periksa apakah setiap kelompok telah dipilih
                for (const groupName in radioGroups) {
                    const isChecked = radioGroups[groupName].some(radio => radio.checked);
                    if (!isChecked) {
                        valid = false;
                        // Temukan tabel row untuk radio button ini
                        const questionId = groupName.match(/\d+/)[0];
                        const row = document.querySelector(`input[name="ratings[${questionId}]"]`).closest('tr');
                        row.classList.add('table-danger');
                    }
                }

                if (!valid) {
                    e.preventDefault();
                    alert('Mohon lengkapi semua penilaian yang diperlukan.');
                    window.scrollTo(0, 0);
                }
            });

            // Hapus highlight ketika memilih rating
            document.querySelectorAll('input[type=radio]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const row = this.closest('tr');
                    if (row) row.classList.remove('table-danger');
                });
            });
        });
    </script>
@endsection
