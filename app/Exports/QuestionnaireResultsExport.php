<?php

namespace App\Exports;

use App\Models\Questionnaire;
use App\Models\Response;
use App\Models\Question;
use App\Models\QuestionnaireCategory;
use App\Models\User;
use App\Models\Suggestion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\DB;

class QuestionnaireResultsExport implements WithMultipleSheets
{
    protected $questionnaireId;

    public function __construct($questionnaireId)
    {
        $this->questionnaireId = $questionnaireId;
    }

    public function sheets(): array
    {
        $sheets = [
            new QuestionStatisticsSheet($this->questionnaireId),
            new RespondentDataSheet($this->questionnaireId),
            new QuestionCodesSheet($this->questionnaireId),
        ];

        // Tambahkan sheet saran untuk tipe tertentu
        $questionnaire = Questionnaire::findOrFail($this->questionnaireId);
        if (in_array($questionnaire->type, ['kepuasan_mitra', 'kepuasan_pengguna_lulusan'])) {
            $sheets[] = new SuggestionsSheet($this->questionnaireId);
        }

        return $sheets;
    }
}

class QuestionStatisticsSheet implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $questionnaireId;

    public function __construct($questionnaireId)
    {
        $this->questionnaireId = $questionnaireId;
    }

    public function collection()
    {
        $questionnaire = Questionnaire::findOrFail($this->questionnaireId);
        $categories = QuestionnaireCategory::all();
        $questions = Question::where('questionnaire_id', $this->questionnaireId)
            ->orderBy('category_id')
            ->orderBy('order')
            ->get();

        $data = collect();
        $questionNumber = 1;

        foreach ($categories as $category) {
            $categoryQuestions = $questions->where('category_id', $category->id)->sortBy('order');

            foreach ($categoryQuestions as $question) {
                $answers = DB::table('responses')
                    ->select('rating as value', DB::raw('count(*) as total'))
                    ->where('question_id', $question->id)
                    ->where('questionnaire_id', $this->questionnaireId)
                    ->groupBy('rating')
                    ->get();

                $stats = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
                $total = 0;
                $sum = 0;

                foreach ($answers as $answer) {
                    $value = (int)$answer->value;
                    if ($value >= 1 && $value <= 4) {
                        $stats[$value] = $answer->total;
                        $total += $answer->total;
                        $sum += $value * $answer->total;
                    }
                }

                $average = $total > 0 ? $sum / $total : 0;

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

                $data->push([
                    'no' => $questionNumber++,
                    'pertanyaan' => $question->question,
                    'kategori' => $category->name,
                    'sangat_baik' => $stats[4] . ' (' . ($total > 0 ? number_format(($stats[4] / $total) * 100, 1) : 0) . '%)',
                    'baik' => $stats[3] . ' (' . ($total > 0 ? number_format(($stats[3] / $total) * 100, 1) : 0) . '%)',
                    'cukup' => $stats[2] . ' (' . ($total > 0 ? number_format(($stats[2] / $total) * 100, 1) : 0) . '%)',
                    'kurang' => $stats[1] . ' (' . ($total > 0 ? number_format(($stats[1] / $total) * 100, 1) : 0) . '%)',
                    'total' => $total,
                    'rata_rata' => number_format($average, 2),
                    'keterangan' => $keterangan
                ]);
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Pertanyaan',
            'Kategori',
            'Sangat Baik (4)',
            'Baik (3)',
            'Cukup (2)',
            'Kurang (1)',
            'Total',
            'Rata-rata',
            'Keterangan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFF2F2F2']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set judul sheet
                $questionnaire = Questionnaire::findOrFail($this->questionnaireId);
                $sheet->setTitle('Statistik Pertanyaan');

                // Insert title row
                $sheet->insertNewRowBefore(1, 2);
                $sheet->setCellValue('A1', 'Lampiran: Data Detail Kuesioner ' . $questionnaire->title);
                $sheet->mergeCells('A1:J1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Auto-size columns
                $columnWidths = [
                    'A' => 5,   // No
                    'B' => 40,  // Pertanyaan
                    'C' => 20,  // Kategori
                    'D' => 15,  // Sangat Baik
                    'E' => 15,  // Baik
                    'F' => 15,  // Cukup
                    'G' => 15,  // Kurang
                    'H' => 8,   // Total
                    'I' => 12,  // Rata-rata
                    'J' => 15   // Keterangan
                ];

                foreach ($columnWidths as $column => $width) {
                    $sheet->getColumnDimension($column)->setWidth($width);
                }

                // Style header
                $sheet->getStyle('A3:J3')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF2F2F2']
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Apply borders to all data
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A3:J{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Center align specific columns
                $sheet->getStyle("A4:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
                $sheet->getStyle("C4:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Kategori
                $sheet->getStyle("D4:J{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Data statistik
            },
        ];
    }
}

class RespondentDataSheet implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $questionnaireId;

    public function __construct($questionnaireId)
    {
        $this->questionnaireId = $questionnaireId;
    }

    public function collection()
    {
        $questionnaire = Questionnaire::findOrFail($this->questionnaireId);
        $responses = Response::where('questionnaire_id', $this->questionnaireId)->get();
        $questions = Question::where('questionnaire_id', $this->questionnaireId)
            ->orderBy('category_id')
            ->orderBy('order')
            ->get();
        $users = User::whereIn('id', $responses->pluck('user_id')->unique())->get();

        $data = collect();
        $respNo = 1;

        foreach ($responses->groupBy('user_id') as $userId => $userResponses) {
            $respUser = $users->firstWhere('id', $userId);
            if (!$respUser) continue;

            // Build row data berdasarkan tipe kuesioner
            $rowData = $this->buildRowDataByType($questionnaire->type, $respUser, $respNo++);

            // Tambahkan jawaban untuk setiap pertanyaan
            foreach ($questions as $question) {
                $answer = $userResponses->firstWhere('question_id', $question->id);
                $rating = $answer ? $answer->rating : '-';
                $rowData['q' . $question->order] = $rating;
            }

            $data->push($rowData);
        }

        return $data;
    }

    private function buildRowDataByType($questionnaireType, $respUser, $respNo)
    {
        $rowData = ['no' => $respNo];

        switch ($questionnaireType) {
            case 'kepuasan_mitra':
                $rowData = array_merge($rowData, [
                    'nama_responden' => $respUser->name ?? '-',
                    'email' => $respUser->email ?? '-',
                    'nama_institusi' => $respUser->nama_instansi ?? '-',
                    'jabatan' => $respUser->jabatan ?? '-',
                    'jenis_mitra' => $respUser->meta_jenis_mitra ?? '-',
                    'jenis_kerjasama' => $respUser->meta_jenis_kerjasama ?? '-',
                    'lingkup_kerjasama' => $respUser->meta_lingkup_kerjasama ?? '-',
                    'periode_kerjasama' => $respUser->meta_periode_kerjasama ?? '-',
                    'no_telepon' => $respUser->no_telepon ?? '-',
                    'alamat' => $respUser->meta_alamat ?? '-'
                ]);
                break;

            case 'kepuasan_pengguna_lulusan':
                $rowData = array_merge($rowData, [
                    'nama_responden' => $respUser->name ?? '-',
                    'email' => $respUser->email ?? '-',
                    'nama_perusahaan' => $respUser->nama_instansi ?? '-',
                    'jabatan' => $respUser->jabatan ?? '-',
                    'nama_alumni' => $respUser->meta_nama_alumni ?? '-',
                    'tahun_lulus_alumni' => $respUser->meta_tahun_lulus_alumni ?? '-',
                    'program_studi_alumni' => $respUser->meta_program_studi_alumni ?? '-',
                    'no_telepon' => $respUser->no_telepon ?? '-'
                ]);
                break;

            default:
                // Data standar untuk mahasiswa, dosen, tendik, alumni
                $fieldTambahan = '-';
                if ($respUser->role === 'alumni') {
                    $fieldTambahan = implode('|', array_filter([
                        $respUser->tahun_lulus,
                        $respUser->domisili,
                        $respUser->npwp
                    ]));
                    $fieldTambahan = $fieldTambahan ?: '-';
                }

                $rowData = array_merge($rowData, [
                    'nim' => $respUser->nim ?? '',
                    'nip' => $respUser->nip ?? '',
                    'nik' => $respUser->nik ?? '',
                    'nama_responden' => $respUser->name ?? '-',
                    'email' => $respUser->email ?? '-',
                    'program_studi' => $respUser->program_studi ?? '-',
                    'no_telepon' => $respUser->no_telepon ?? '-',
                    'field_tambahan' => $fieldTambahan
                ]);
                break;
        }

        return $rowData;
    }

    public function headings(): array
    {
        $questionnaire = Questionnaire::findOrFail($this->questionnaireId);
        $questions = Question::where('questionnaire_id', $this->questionnaireId)
            ->orderBy('category_id')
            ->orderBy('order')
            ->get();

        // Tentukan header berdasarkan tipe kuesioner
        $headings = [];
        switch ($questionnaire->type) {
            case 'kepuasan_mitra':
                $headings = ['No', 'Nama Responden', 'Email', 'Nama Institusi', 'Jabatan', 'Jenis Mitra', 'Jenis Kerjasama', 'Lingkup Kerjasama', 'Periode Kerjasama', 'No Telepon', 'Alamat'];
                break;
            case 'kepuasan_pengguna_lulusan':
                $headings = ['No', 'Nama Responden', 'Email', 'Nama Perusahaan/Institusi', 'Jabatan', 'Nama Alumni FIK', 'Tahun Lulus Alumni', 'Program Studi Alumni', 'No Telepon'];
                break;
            default:
                $headings = ['No', 'NIM', 'NIP', 'NIK', 'Nama Responden', 'Email', 'Program Studi', 'No Telepon', 'Field Tambahan'];
                break;
        }

        // Tambahkan header untuk pertanyaan
        $questionNumber = 1;
        foreach ($questions as $question) {
            $headings[] = 'Q' . $questionNumber++;
        }

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFF2F2F2']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $questionnaire = Questionnaire::findOrFail($this->questionnaireId);
                $sheet = $event->sheet->getDelegate();

                // Set judul sheet
                $sheet->setTitle('Data Responden');

                // Insert title row
                $sheet->insertNewRowBefore(1, 2);
                $sheet->setCellValue('A1', 'Data Responden (Tabulasi)');
                $lastColumn = $sheet->getHighestColumn(3);
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Set column widths berdasarkan tipe kuesioner
                $this->setColumnWidths($sheet, $questionnaire->type);

                // Style header
                $lastColumn = $sheet->getHighestColumn(3);
                $sheet->getStyle("A3:{$lastColumn}3")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF2F2F2']
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Apply borders to all data
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();
                $sheet->getStyle("A3:{$lastColumn}{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Center align specific columns
                $sheet->getStyle("A4:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No

                // Center align jawaban (kolom setelah data user)
                $questions = Question::where('questionnaire_id', $this->questionnaireId)->get();
                if ($questions->count() > 0) {
                    $questionnaire = Questionnaire::findOrFail($this->questionnaireId);
                    $startQuestionColIndex = $this->getQuestionStartColumnIndex($questionnaire->type);
                    $endQuestionColIndex = $startQuestionColIndex + $questions->count() - 1;

                    $startQuestionCol = $this->getColumnLetter($startQuestionColIndex);
                    $endQuestionCol = $this->getColumnLetter($endQuestionColIndex);

                    $sheet->getStyle("{$startQuestionCol}4:{$endQuestionCol}{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }

    private function getQuestionStartColumnIndex($questionnaireType)
    {
        switch ($questionnaireType) {
            case 'kepuasan_mitra':
                return 12; // Kolom L (index 12, karena A=1)
            case 'kepuasan_pengguna_lulusan':
                return 10; // Kolom J (index 10)
            default:
                return 10; // Kolom J (index 10)
        }
    }

    private function setColumnWidths(Worksheet $sheet, $questionnaireType)
    {
        $questions = Question::where('questionnaire_id', $this->questionnaireId)->get();

        switch ($questionnaireType) {
            case 'kepuasan_mitra':
                $columnWidths = [
                    'A' => 5,  // No
                    'B' => 20, // Nama Responden
                    'C' => 25, // Email
                    'D' => 20, // Nama Institusi
                    'E' => 15, // Jabatan
                    'F' => 15, // Jenis Mitra
                    'G' => 18, // Jenis Kerjasama
                    'H' => 18, // Lingkup Kerjasama
                    'I' => 15, // Periode Kerjasama
                    'J' => 15, // No Telepon
                    'K' => 20  // Alamat
                ];
                $startQuestionColIndex = 11; // Kolom L (index 11)
                break;

            case 'kepuasan_pengguna_lulusan':
                $columnWidths = [
                    'A' => 5,  // No
                    'B' => 20, // Nama Responden
                    'C' => 25, // Email
                    'D' => 20, // Nama Perusahaan
                    'E' => 15, // Jabatan
                    'F' => 20, // Nama Alumni
                    'G' => 15, // Tahun Lulus Alumni
                    'H' => 20, // Program Studi Alumni
                    'I' => 15  // No Telepon
                ];
                $startQuestionColIndex = 9; // Kolom J (index 9)
                break;

            default:
                $columnWidths = [
                    'A' => 5,  // No
                    'B' => 12, // NIM
                    'C' => 12, // NIP
                    'D' => 12, // NIK
                    'E' => 20, // Nama Responden
                    'F' => 25, // Email
                    'G' => 20, // Program Studi
                    'H' => 15, // No Telepon
                    'I' => 20  // Field Tambahan
                ];
                $startQuestionColIndex = 9; // Kolom J (index 9)
                break;
        }

        // Set width untuk kolom user data
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        // Set width untuk kolom pertanyaan menggunakan coordinate helper
        for ($i = 0; $i < $questions->count(); $i++) {
            $colIndex = $startQuestionColIndex + $i;
            $columnLetter = $this->getColumnLetter($colIndex);
            $sheet->getColumnDimension($columnLetter)->setWidth(8);
        }
    }

    private function getQuestionStartColumn($questionnaireType)
    {
        switch ($questionnaireType) {
            case 'kepuasan_mitra':
                return $this->getColumnLetter(11); // Setelah alamat (kolom K)
            case 'kepuasan_pengguna_lulusan':
                return $this->getColumnLetter(9); // Setelah no_telepon (kolom I)
            default:
                return $this->getColumnLetter(9); // Setelah field tambahan (kolom I)
        }
    }

    private function getColumnLetter($columnIndex)
    {
        if ($columnIndex <= 0) {
            return 'A';
        }

        $columnLetter = '';
        while ($columnIndex > 0) {
            $columnIndex--; // Convert to 0-based indexing
            $columnLetter = chr(65 + ($columnIndex % 26)) . $columnLetter;
            $columnIndex = intval($columnIndex / 26);
        }
        return $columnLetter;
    }
}

class QuestionCodesSheet implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $questionnaireId;

    public function __construct($questionnaireId)
    {
        $this->questionnaireId = $questionnaireId;
    }

    public function collection()
    {
        $questions = Question::where('questionnaire_id', $this->questionnaireId)
            ->orderBy('category_id')
            ->orderBy('order')
            ->get();
        $categories = QuestionnaireCategory::all();

        $data = collect();
        $questionNumber = 1;

        foreach ($questions as $question) {
            $category = $categories->firstWhere('id', $question->category_id);

            $data->push([
                'kode' => 'Q' . $questionNumber++,
                'pertanyaan' => $question->question,
                'kategori' => $category ? $category->name : '-'
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Pertanyaan',
            'Kategori'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFF2F2F2']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set judul sheet
                $sheet->setTitle('Keterangan Kode');

                // Insert title row
                $sheet->insertNewRowBefore(1, 2);
                $sheet->setCellValue('A1', 'Keterangan Kode Pertanyaan');
                $sheet->mergeCells('A1:C1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(10); // Kode
                $sheet->getColumnDimension('B')->setWidth(60); // Pertanyaan
                $sheet->getColumnDimension('C')->setWidth(25); // Kategori

                // Style header
                $sheet->getStyle('A3:C3')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF2F2F2']
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Apply borders to all data
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A3:C{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Center align kode and kategori columns
                $sheet->getStyle("A4:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Kode
                $sheet->getStyle("C4:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Kategori

                // Wrap text for pertanyaan column
                $sheet->getStyle("B4:B{$lastRow}")->getAlignment()->setWrapText(true);
            },
        ];
    }
}

class SuggestionsSheet implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $questionnaireId;

    public function __construct($questionnaireId)
    {
        $this->questionnaireId = $questionnaireId;
    }

    public function collection()
    {
        $suggestions = Suggestion::where('questionnaire_id', $this->questionnaireId)
            ->with('user')
            ->get();

        $data = collect();
        $no = 1;

        foreach ($suggestions as $suggestion) {
            $data->push([
                'no' => $no++,
                'nama_responden' => $suggestion->user->name ?? 'Anonim',
                'saran' => $suggestion->content
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Responden',
            'Saran dan Masukan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFF2F2F2']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set judul sheet
                $sheet->setTitle('Saran dan Masukan');

                // Insert title row
                $sheet->insertNewRowBefore(1, 2);
                $sheet->setCellValue('A1', 'Saran dan Masukan');
                $sheet->mergeCells('A1:C1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(5);  // No
                $sheet->getColumnDimension('B')->setWidth(25); // Nama Responden
                $sheet->getColumnDimension('C')->setWidth(60); // Saran

                // Style header
                $sheet->getStyle('A3:C3')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF2F2F2']
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Apply borders to all data
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A3:C{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Center align no column
                $sheet->getStyle("A4:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Wrap text for saran column
                $sheet->getStyle("C4:C{$lastRow}")->getAlignment()->setWrapText(true);
            },
        ];
    }
}
