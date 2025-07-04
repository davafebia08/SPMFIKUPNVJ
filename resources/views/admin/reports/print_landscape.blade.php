<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lampiran {{ $title ?? 'Laporan Kuesioner' }}</title>
    <style>
        @page {
            size: 29.7cm 21cm landscape;
            margin: 1cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        .page-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .content-table th,
        .content-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            text-align: left;
            vertical-align: top;
        }

        .content-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }

        .compact-table {
            font-size: 7pt;
        }

        .compact-header {
            writing-mode: vertical-lr;
            transform: rotate(180deg);
            white-space: nowrap;
            max-width: 20px;
            padding: 5px 0;
        }

        .responden-table {
            font-size: 6pt;
        }

        .responden-table th,
        .responden-table td {
            padding: 2px 3px;
        }
    </style>
</head>

<body>
    <div class="page-title">Lampiran: Data Detail Kuesioner {{ $title ?? 'Evaluasi' }}</div>

    <!-- Tabel Pertanyaan dan Statistik -->
    <table class="content-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="30%">Pertanyaan</th>
                <th width="15%">Kategori</th>
                <th width="8%">Sangat Baik (4)</th>
                <th width="8%">Baik (3)</th>
                <th width="8%">Cukup (2)</th>
                <th width="8%">Kurang (1)</th>
                <th width="5%">Total</th>
                <th width="5%">Rata-rata</th>
                <th width="10%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $questionNumber = 1; @endphp
            @foreach ($categories as $category)
                @php
                    $categoryQuestions = $questions->where('category_id', $category->id)->sortBy('order');
                @endphp

                @foreach ($categoryQuestions as $question)
                    @php
                        $stats = $questionStats[$question->id]['stats'] ?? [1 => 0, 2 => 0, 3 => 0, 4 => 0];
                        $average = $questionStats[$question->id]['average'] ?? 0;
                        $total = $questionStats[$question->id]['total'] ?? 0;

                        // Menentukan keterangan berdasarkan rata-rata
                        $keterangan = '';
                        if ($average >= 3.5) {
                            $keterangan = 'Sangat Baik';
                        } elseif ($average >= 2.5) {
                            $keterangan = 'Baik';
                        } elseif ($average >= 1.5) {
                            $keterangan = 'Cukup';
                        } else {
                            $keterangan = 'Kurang';
                        }
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $questionNumber++ }}</td>
                        <td>{{ $question->question }}</td>
                        <td style="text-align: center;">{{ $category->name }}</td>
                        <td style="text-align: center;">{{ $stats[4] }} ({{ $total > 0 ? number_format(($stats[4] / $total) * 100, 1) : 0 }}%)
                        </td>
                        <td style="text-align: center;">{{ $stats[3] }} ({{ $total > 0 ? number_format(($stats[3] / $total) * 100, 1) : 0 }}%)
                        </td>
                        <td style="text-align: center;">{{ $stats[2] }} ({{ $total > 0 ? number_format(($stats[2] / $total) * 100, 1) : 0 }}%)
                        </td>
                        <td style="text-align: center;">{{ $stats[1] }} ({{ $total > 0 ? number_format(($stats[1] / $total) * 100, 1) : 0 }}%)
                        </td>
                        <td style="text-align: center;">{{ $total }}</td>
                        <td style="text-align: center;">{{ number_format($average, 2) }}</td>
                        <td style="text-align: center;">{{ $keterangan }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- Tabel Data Responden dengan informasi lengkap berdasarkan tipe kuesioner -->
    <div class="page-title">Data Responden (Tabulasi)</div>

    @php
        // Tentukan header berdasarkan tipe kuesioner
        $responseHeaders = [];
        switch ($questionnaire->type) {
            case 'kepuasan_mitra':
                $responseHeaders = [
                    'No',
                    'Nama Responden',
                    'Email',
                    'Nama Institusi',
                    'Jabatan',
                    'Jenis Mitra',
                    'Jenis Kerjasama',
                    'Lingkup Kerjasama',
                    'Periode Kerjasama',
                    'No Telepon',
                    'Alamat',
                ];
                break;
            case 'kepuasan_pengguna_lulusan':
                $responseHeaders = [
                    'No',
                    'Nama Responden',
                    'Email',
                    'Nama Perusahaan/Institusi',
                    'Jabatan',
                    'Nama Alumni FIK',
                    'Tahun Lulus Alumni',
                    'Program Studi Alumni',
                    'No Telepon',
                ];
                break;
            default:
                $responseHeaders = ['No', 'NIM', 'NIP', 'NIK', 'Nama Responden', 'Email', 'Program Studi', 'No Telepon', 'Field Tambahan'];
                break;
        }

        // Tambahkan header untuk pertanyaan
        $questionNumber = 1;
        foreach ($questions as $question) {
            $responseHeaders[] = 'Q' . $questionNumber;
            $questionNumber++;
        }
    @endphp

    <table class="content-table responden-table">
        <thead>
            <tr>
                @foreach ($responseHeaders as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php $respNo = 1; @endphp
            @foreach ($responses->groupBy('user_id') as $userId => $userResponses)
                @php
                    $respUser = $users->firstWhere('id', $userId);
                    if (!$respUser) {
                        continue;
                    }
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $respNo++ }}</td>

                    @if ($questionnaire->type === 'kepuasan_mitra')
                        <!-- Data untuk Mitra -->
                        <td>{{ $respUser->name ?? '-' }}</td>
                        <td>{{ $respUser->email ?? '-' }}</td>
                        <td>{{ $respUser->nama_instansi ?? '-' }}</td>
                        <td>{{ $respUser->jabatan ?? '-' }}</td>
                        <td>{{ $respUser->meta_jenis_mitra ?? '-' }}</td>
                        <td>{{ $respUser->meta_jenis_kerjasama ?? '-' }}</td>
                        <td>{{ $respUser->meta_lingkup_kerjasama ?? '-' }}</td>
                        <td>{{ $respUser->meta_periode_kerjasama ?? '-' }}</td>
                        <td>{{ $respUser->no_telepon ?? '-' }}</td>
                        <td>{{ $respUser->meta_alamat ?? '-' }}</td>
                    @elseif($questionnaire->type === 'kepuasan_pengguna_lulusan')
                        <!-- Data untuk Pengguna Lulusan -->
                        <td>{{ $respUser->name ?? '-' }}</td>
                        <td>{{ $respUser->email ?? '-' }}</td>
                        <td>{{ $respUser->nama_instansi ?? '-' }}</td>
                        <td>{{ $respUser->jabatan ?? '-' }}</td>
                        <td>{{ $respUser->meta_nama_alumni ?? '-' }}</td>
                        <td>{{ $respUser->meta_tahun_lulus_alumni ?? '-' }}</td>
                        <td>{{ $respUser->meta_program_studi_alumni ?? '-' }}</td>
                        <td>{{ $respUser->no_telepon ?? '-' }}</td>
                    @else
                        <!-- Data Standar (mahasiswa, dosen, tendik, alumni, dll) -->
                        <td>{{ $respUser->nim ?? '' }}</td>
                        <td>{{ $respUser->nip ?? '' }}</td>
                        <td>{{ $respUser->nik ?? '' }}</td>
                        <td>{{ $respUser->name ?? '-' }}</td>
                        <td>{{ $respUser->email ?? '-' }}</td>
                        <td>{{ $respUser->program_studi ?? '-' }}</td>
                        <td>{{ $respUser->no_telepon ?? '-' }}</td>
                        <td>
                            @if ($respUser->role === 'alumni')
                                @php
                                    $fieldTambahan = implode('|', array_filter([$respUser->tahun_lulus, $respUser->domisili, $respUser->npwp]));
                                @endphp
                                {{ $fieldTambahan ?: '-' }}
                            @else
                                -
                            @endif
                        </td>
                    @endif

                    <!-- Data Jawaban untuk semua tipe -->
                    @foreach ($questions as $question)
                        @php
                            $answer = $userResponses->firstWhere('question_id', $question->id);
                            $rating = $answer ? $answer->rating : '-';
                        @endphp
                        <td style="text-align: center;">{{ $rating }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($questionnaire->type === 'kepuasan_mitra' || $questionnaire->type === 'kepuasan_pengguna_lulusan')
        <div class="page-break"></div>

        <!-- Tabel Saran dan Masukan untuk Mitra atau Pengguna Lulusan -->
        <div class="page-title">Saran dan Masukan</div>

        <table class="content-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="25%">Nama Responden</th>
                    <th width="70%">Saran dan Masukan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $saranNo = 1;
                    $suggestions = \App\Models\Suggestion::where('questionnaire_id', $questionnaire->id)->with('user')->get();
                @endphp

                @forelse($suggestions as $suggestion)
                    <tr>
                        <td style="text-align: center;">{{ $saranNo++ }}</td>
                        <td>{{ $suggestion->user->name ?? 'Anonim' }}</td>
                        <td>{{ $suggestion->content }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; font-style: italic;">Tidak ada saran dan masukan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <div class="page-break"></div>

    <!-- Keterangan Kode Pertanyaan -->
    <div class="page-title">Keterangan Kode Pertanyaan</div>

    <table class="content-table">
        <thead>
            <tr>
                <th width="10%">Kode</th>
                <th width="70%">Pertanyaan</th>
                <th width="20%">Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($questions as $question)
                <tr>
                    <td style="text-align: center;">Q{{ $loop->iteration }}</td>
                    <td>{{ $question->question }}</td>
                    <td style="text-align: center;">{{ $categories->firstWhere('id', $question->category_id)->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Keterangan Skala Penilaian -->
    <div style="margin-top: 20px;">
        <h3>Keterangan Skala Penilaian:</h3>
        <ul style="margin-left: 20px;">
            <li>4 = Sangat Baik</li>
            <li>3 = Baik</li>
            <li>2 = Cukup</li>
            <li>1 = Kurang</li>
            <li>- = Tidak Menjawab</li>
        </ul>
    </div>

</body>

</html>
