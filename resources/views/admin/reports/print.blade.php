<!-- resources/views/admin/reports/print.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Laporan Kuesioner' }}</title>
    <style>
        @page {
            margin: 2.5cm 2.5cm 2.5cm 2.5cm;
            /* Margin standar untuk halaman konten */
            size: A4;
            /* Ukuran kertas A4 */
        }

        @page :first {
            margin: 0;
            /* Margin 0 untuk halaman pertama (cover) */
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
            font-size: 12pt;
            margin: 0;
            padding: 0;
        }

        .cover-page {
            width: 100%;
            height: 100%;
            position: relative;
            padding: 0;
            margin: 0;
        }

        .cover-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            margin: 0;
            padding: 0;
        }

        .page-break {
            page-break-after: always;
            clear: both;
        }

        .content-page {
            margin-top: 0;
            /* Sesuaikan margin atas konten */
        }

        h1 {
            text-align: center;
            font-size: 14pt;
            margin-top: 30px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        h2 {
            font-size: 12pt;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        h3 {
            font-size: 12pt;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        p {
            text-align: justify;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 10pt;
        }

        /* Tabel untuk konten tabel */
        .content-table,
        .content-table th,
        .content-table td {
            border: 1px solid #000;
        }

        .content-table th,
        .content-table td {
            padding: 8px;
            text-align: left;
        }

        .content-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* Tabel daftar isi tanpa border */
        .toc {
            width: 100%;
            border: none;
            margin: 20px 0;
        }

        .toc td {
            padding: 5px 0;
            border: none;
        }

        .toc-text {
            white-space: nowrap;
            padding-right: 5px;
        }

        .toc-line {
            width: 100%;
            border: none;
            /* Ini akan membuat garis titik-titik */
            background-image: linear-gradient(to right, #000 10%, rgba(255, 255, 255, 0) 0%);
            background-position: bottom;
            background-size: 5px 1px;
            background-repeat: repeat-x;
            height: 1em;
        }

        .toc-page {
            white-space: nowrap;
            text-align: right;
            padding-left: 5px;
        }

        .chart-container {
            width: 100%;
            margin: 20px 0;
            text-align: center;
        }

        .chart-container img {
            max-width: 90%;
            height: auto;
        }

        .chart-description {
            margin: 15px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            text-align: justify;
            font-size: 11pt;
            line-height: 1.4;
        }

        .signature {
            margin-top: 50px;
            text-align: right;
            margin-right: 50px;
        }

        .signature p {
            margin: 5px 0;
            text-align: right;
        }

        .signature .name {
            margin-top: 60px;
            font-weight: bold;
        }

        .lampiran {
            margin-top: 30px;
        }

        .lampiran-image {
            width: 100%;
            margin: 20px 0;
            text-align: center;
        }

        ol,
        ul {
            margin-left: 20px;
            padding-left: 20px;
        }

        /* Tambahkan gaya khusus untuk judul halaman seperti yang tampak di contoh PDF */
        .page-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 30px;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <!-- Cover Page dengan gambar yang berbeda untuk setiap tipe kuesioner -->
    <div class="cover-page">
        @php
            $coverImages = [
                'evaluasi_dosen' => 'Kuesioner Evaluasi Dosen oleh Mahasiswa.jpg',
                'layanan_fakultas' => 'Kuesioner Layanan Fakultas.jpg',
                'elom' => 'Kuesioner Evaluasi Layanan oleh Mahasiswa.jpg',
                'elta' => 'Kuesioner Evaluasi Layanan Tugas Akhir.jpg',
                'kepuasan_dosen' => 'Kuesioner Kepuasan Dosen.jpg',
                'kepuasan_tendik' => 'Kuesioner Kepuasan Tenaga Kependidikan.jpg',
                'kepuasan_alumni' => 'Kuesioner Kepuasan Alumni.jpg',
                'kepuasan_pengguna_lulusan' => 'Kuesioner Kepuasan Pengguna Lulusan.jpg',
                'kepuasan_mitra' => 'Kuesioner Kepuasan Mitra Kerjasama.jpg',
            ];

            $coverImage = isset($coverImages[$questionnaire->type]) ? $coverImages[$questionnaire->type] : 'default_cover.png';
        @endphp

        <!-- Gunakan URL absolut dengan domain lengkap untuk gambar cover -->
        <img src="{{ public_path('images/covers/' . $coverImage) }}" alt="Cover Laporan {{ $questionnaire->title }}" class="cover-image">
    </div>

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Daftar Isi dengan format yang diperbaiki menggunakan tabel -->
    <div class="content-page">
        <div class="page-title">DAFTAR ISI</div>
        <table class="toc">
            <tr>
                <td class="toc-text">DAFTAR ISI</td>
                <td class="toc-line"></td>
                <td class="toc-page">2</td>
            </tr>
            <tr>
                <td class="toc-text">KATA PENGANTAR</td>
                <td class="toc-line"></td>
                <td class="toc-page">3</td>
            </tr>
            <tr>
                <td class="toc-text">BAB I PENDAHULUAN</td>
                <td class="toc-line"></td>
                <td class="toc-page">4</td>
            </tr>
            <tr>
                <td class="toc-text">&nbsp;&nbsp;&nbsp;&nbsp;1.1. Latar Belakang</td>
                <td class="toc-line"></td>
                <td class="toc-page">4</td>
            </tr>
            <tr>
                <td class="toc-text">&nbsp;&nbsp;&nbsp;&nbsp;1.2. Tujuan</td>
                <td class="toc-line"></td>
                <td class="toc-page">4</td>
            </tr>
            <tr>
                <td class="toc-text">&nbsp;&nbsp;&nbsp;&nbsp;1.3. Manfaat</td>
                <td class="toc-line"></td>
                <td class="toc-page">4</td>
            </tr>
            <tr>
                <td class="toc-text">&nbsp;&nbsp;&nbsp;&nbsp;1.4. Ruang Lingkup</td>
                <td class="toc-line"></td>
                <td class="toc-page">5</td>
            </tr>
            <tr>
                <td class="toc-text">BAB II METODE</td>
                <td class="toc-line"></td>
                <td class="toc-page">6</td>
            </tr>
            <tr>
                <td class="toc-text">&nbsp;&nbsp;&nbsp;&nbsp;2.1. Teknik Pengumpulan Data</td>
                <td class="toc-line"></td>
                <td class="toc-page">6</td>
            </tr>
            <tr>
                <td class="toc-text">&nbsp;&nbsp;&nbsp;&nbsp;2.2. Instrumen Survey</td>
                <td class="toc-line"></td>
                <td class="toc-page">6</td>
            </tr>
            <tr>
                <td class="toc-text">BAB III HASIL DAN PEMBAHASAN</td>
                <td class="toc-line"></td>
                <td class="toc-page">8</td>
            </tr>
            <tr>
                <td class="toc-text">&nbsp;&nbsp;&nbsp;&nbsp;3.1. Hasil</td>
                <td class="toc-line"></td>
                <td class="toc-page">8</td>
            </tr>
            <tr>
                <td class="toc-text">&nbsp;&nbsp;&nbsp;&nbsp;3.2. Analisa & Pembahasan</td>
                <td class="toc-line"></td>
                <td class="toc-page">15</td>
            </tr>
            <tr>
                <td class="toc-text">BAB IV PENUTUP</td>
                <td class="toc-line"></td>
                <td class="toc-page">16</td>
            </tr>
            <tr>
                <td class="toc-text">&nbsp;&nbsp;&nbsp;&nbsp;4.1. Simpulan</td>
                <td class="toc-line"></td>
                <td class="toc-page">16</td>
            </tr>
            <tr>
                <td class="toc-text">&nbsp;&nbsp;&nbsp;&nbsp;4.2. Rencana Tindak Lanjut (RTL)</td>
                <td class="toc-line"></td>
                <td class="toc-page">16</td>
            </tr>
            <tr>
                <td class="toc-text">Lampiran</td>
                <td class="toc-line"></td>
                <td class="toc-page">17</td>
            </tr>
        </table>
    </div>

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Kata Pengantar -->
    <div class="content-page">
        <div class="page-title">KATA PENGANTAR</div>
        <p>Puji syukur kami panjatkan ke hadirat Tuhan Yang Maha Esa atas limpahan rahmat dan karunia-Nya sehingga penyusunan laporan
            {{ $title ?? 'Evaluasi Dosen oleh Mahasiswa (EDOM)' }} ini dapat diselesaikan dengan baik. Laporan ini merupakan salah satu upaya untuk
            meningkatkan kualitas pembelajaran dan pengajaran di lingkungan universitas kita.</p>

        <p>{{ $title ?? 'Evaluasi Dosen oleh Mahasiswa (EDOM)' }} adalah alat penting dalam proses penjaminan mutu pendidikan. Melalui
            {{ $survey_type ?? 'EDOM' }}, {{ $responden ?? 'mahasiswa' }} memiliki kesempatan untuk memberikan masukan yang konstruktif tentang
            {{ $object ?? 'kinerja dosen dalam proses pembelajaran' }}. Masukan yang diperoleh dari {{ $survey_type ?? 'EDOM' }} diharapkan dapat
            menjadi bahan pertimbangan dalam
            {{ $improvement_for ?? 'pengembangan profesionalisme dosen, peningkatan metode pengajaran, serta perbaikan kurikulum yang lebih responsif terhadap kebutuhan mahasiswa' }}.
        </p>

        <p>Kami menyadari bahwa penyusunan laporan ini tidak lepas dari dukungan berbagai pihak. Oleh karena itu, kami mengucapkan terima kasih yang
            sebesar-besarnya kepada semua {{ $responden ?? 'mahasiswa' }} yang telah berpartisipasi dalam pengisian kuesioner
            {{ $survey_type ?? 'EDOM' }} dengan penuh tanggung jawab. Ucapan terima kasih juga kami sampaikan kepada
            {{ $stakeholders ?? 'para dosen yang dengan terbuka menerima hasil evaluasi ini sebagai bagian dari upaya untuk terus meningkatkan kualitas pengajaran' }}.
        </p>

        <p>Kami berharap, laporan ini dapat memberikan manfaat yang besar bagi pengembangan institusi pendidikan kita. Saran dan kritik yang membangun
            sangat kami harapkan demi perbaikan di masa mendatang.</p>

        <p>Akhir kata, semoga laporan ini dapat menjadi langkah awal menuju peningkatan kualitas pendidikan yang lebih baik dan dapat menciptakan
            suasana akademik yang lebih kondusif dan produktif.</p>
    </div>

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Bab I: Pendahuluan -->
    <div class="content-page">
        <div class="page-title">BAB I PENDAHULUAN</div>

        <h2>1.1. Latar Belakang</h2>
        <p>{{ $latar_belakang ?? 'Kuesioner kepuasan evaluasi dosen oleh mahasiswa disusun untuk mengetahui bagaimana respon dan penilaian mahasiswa terhadap aktifitas pembelajaran selama 1 semester untuk suatu mata kuliah tertentu. EDOM dilakukan 2 (dua) kali dalam 1 semester, yaitu sebelum Ujian Tengah Semester dan sebelum Ujian Akhir Semester. Pelaksanaan EDOM melalui Sistem Informasi Akademik (SIAKAD).' }}
        </p>

        <h2>1.2. Tujuan</h2>
        <p>{{ $tujuan ??'EDOM (Evaluasi Dosen oleh Mahasiswa) adalah instrumen penting yang digunakan oleh institusi pendidikan tinggi untuk mengumpulkan umpan balik dari mahasiswa mengenai kinerja dosen. Tujuan utama dari EDOM adalah untuk meningkatkan kualitas pembelajaran dengan memastikan bahwa pengajaran yang diberikan memenuhi standar yang diharapkan dan relevan dengan kebutuhan mahasiswa. Melalui EDOM, mahasiswa memiliki kesempatan untuk memberikan penilaian yang jujur tentang berbagai aspek pengajaran, seperti kemampuan dosen dalam menyampaikan materi, penggunaan metode pengajaran yang efektif, interaksi dengan mahasiswa, dan penguasaan materi. Data yang diperoleh dari EDOM digunakan oleh manajemen universitas untuk mengidentifikasi kekuatan dan kelemahan dosen, serta untuk merancang program pengembangan profesional yang sesuai. Selain itu, hasil EDOM juga dapat memotivasi dosen untuk terus meningkatkan kualitas pengajaran mereka, karena mereka mendapatkan umpan balik langsung dari mahasiswa yang mereka ajar. Dengan demikian, EDOM berfungsi sebagai alat yang efektif untuk memastikan bahwa proses pendidikan berjalan dengan baik dan berorientasi pada peningkatan berkelanjutan.' }}
        </p>

        <h2>1.3. Manfaat</h2>
        <p>{{ $manfaat ??'Manfaat dari EDOM (Evaluasi Dosen oleh Mahasiswa) sangat signifikan dalam konteks peningkatan kualitas pendidikan tinggi. EDOM memberikan kesempatan bagi mahasiswa untuk menyuarakan pendapat mereka tentang efektivitas pengajaran dosen, yang mencakup berbagai aspek seperti kejelasan dalam menyampaikan materi, metode pengajaran, interaksi dengan mahasiswa, dan penguasaan terhadap subjek yang diajarkan. Umpan balik ini sangat berharga bagi dosen karena dapat digunakan untuk mengevaluasi dan memperbaiki pendekatan pengajaran mereka, memastikan bahwa kebutuhan dan harapan mahasiswa terpenuhi. Selain itu, data dari EDOM juga membantu manajemen universitas dalam membuat keputusan yang lebih baik terkait pelatihan dan pengembangan dosen, serta dalam proses evaluasi kinerja dosen secara keseluruhan. Secara keseluruhan, EDOM tidak hanya berkontribusi pada peningkatan kualitas pengajaran, tetapi juga memperkuat hubungan antara dosen dan mahasiswa melalui dialog yang konstruktif, sehingga menciptakan lingkungan akademik yang lebih baik dan lebih responsif terhadap kebutuhan pembelajaran.' }}
        </p>

        <h2>1.4. Ruang Lingkup</h2>
        <p>Aspek yang dinilai yaitu terdiri dari:</p>
        <ol>
            <li>Keandalan (Reliability)</li>
            <li>Daya Tanggap (Responsiveness)</li>
            <li>Kepastian (Assurance)</li>
            <li>Empati (Empathy)</li>
            <li>Sarana (Tangible)</li>
        </ol>

        <p>{{ $ruang_lingkup_detail ?? 'Kegiatan survey kepuasan merupakan salah satu cara mengukur tingkat kepuasan stakeholder (customer satisfaction) terhadap layanan yang diberikan oleh UPNVJ (Dosen, Tendik, sarana prasarana, dan manajemen pengelola) dalam proses bisnis yang dilakukan. Menurut Valerie Zeithaml, A. Parasuraman, dan Leonard Berry (1988) kualitas layanan memiliki 5 dimensi yaitu: reliability (keandalan), responsiveness (daya tanggap), assurance (kepastian), empathy (empati) dan tangible (berwujud). Reliability (keandalan) adalah kemampuan untuk memberikan jasa yang handal dan dapat dipercaya. Responsiveness (daya tanggap) yaitu kesadaran dan keinginan untuk membantu customer dan memberikan jasa dengan cepat dan tepat. Assurance (kepastian) adalah pengetahuan, sikap dan kemampuan seseorang untuk menimbulkan kepercayaan dan keyakinan. Empathy (empati) adalah sikap peduli dan perhatian yang diberikan kepada customer.' }}
        </p>
    </div>

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Bab II: Metode -->
    <div class="content-page">
        <div class="page-title">BAB II METODE</div>

        <p>{{ $metode_intro ?? 'Metode evaluasi menggunakan kuesioner yang akan diisi oleh stakeholder sesuai dengan jenis kuesioner yang telah disusun. Kuesioner terdiri dari butir-butir pertanyaan yang mewakili 5 dimensi penilaian kualitas pelayanan tersebut di atas.' }}
        </p>

        <h2>2.1. Teknik Pengumpulan Data</h2>
        <p>{{ $teknik_pengumpulan_data ?? 'Pengumpulan data dalam penelitian sosial untuk mengukur kepuasan stakeholder dilakukan dengan berbagai cara, antara lain: Kuesioner, Studi Pustaka (Ferdinand, 2014). Untuk memperoleh gambaran dan penilaian kualitas layanan Fakultas Ilmu Komputer UPN Veteran Jakarta kepada para stakeholders maka teknik pengumpulan data yang digunakan adalah menggunakan penyebaran kuesioner. Jumlah responden yang cukup banyak menjadi alasan pula untuk menggunakan penyebaran kuesioner. Teknik ini dipilih karena mudah dan sederhana, serta dapat dengan cepat dilakukan dan diketahui hasilnya. Media untuk penyebaran dan pengembalian kuesioner dapat menggunakan e-mail, telepon, atau aplikasi survey (Google Form, JotForm, Crowdsignal, Dislack, Typeform, Engageform, Responter, SurveyMonkey, Cognito Forms, atau MS Office Forms).' }}
        </p>

        <!-- Bagian Instrumen Survey dalam resources/views/admin/reports/print.blade.php -->

        <h2>2.2. Instrumen Survey</h2>
        <p>{{ $instrumen_survey_intro ?? 'Analisis ' . ($title ?? 'Evaluasi Dosen oleh Mahasiswa (EDOM)') . ' berdasarkan pada pedoman survey kepuasan Disediakan oleh LP3M UPN Veteran jakarta' }}
        </p>

        <p>Aspek yang dinilai yaitu terdiri dari:</p>
        <ol>
            <li>Keandalan (Reliability)</li>
            <li>Daya Tanggap (Responsiveness)</li>
            <li>Kepastian (Assurance)</li>
            <li>Empati (Empathy)</li>
            <li>Sarana (Tangible)</li>
        </ol>

        <table class="content-table">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Indikator yang dinilai</th>
                    <th colspan="4">Penilaian</th>
                </tr>
                <tr>
                    <th>Sangat Baik [4]</th>
                    <th>Baik [3]</th>
                    <th>Cukup [2]</th>
                    <th>Kurang [1]</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $questionNumber = 1;
                @endphp

                @forelse($categories as $category)
                    <tr>
                        <td colspan="6"><strong>{{ $category->name }}</strong></td>
                    </tr>

                    @php
                        $categoryQuestions = $questions->where('category_id', $category->id)->sortBy('order');
                    @endphp

                    @forelse($categoryQuestions as $questionItem)
                        <tr>
                            <td>{{ $questionNumber++ }}</td>
                            <td>{{ $questionItem->question }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">Tidak ada pertanyaan untuk kategori ini</td>
                        </tr>
                    @endforelse
                @empty
                    <!-- Fallback jika tidak ada kategori -->
                    <tr>
                        <td colspan="6">Tidak ada data kategori pertanyaan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Bagian Hasil dan Pembahasan dalam resources/views/admin/reports/print.blade.php -->
    <div class="content-page">
        <div class="page-title">BAB III HASIL DAN PEMBAHASAN</div>

        <h2>3.1. Hasil</h2>
        <p>Berikut data survey yang disajikan dalam bentuk tabulasi data atau grafik.</p>

        <!-- Iterasi melalui setiap kategori -->
        @foreach ($categories as $category)
            <h3>3.1.{{ $loop->iteration }} {{ $category->name }}</h3>

            @if (isset($categoryResults[$category->id]) && count($categoryResults[$category->id]) > 0)
                @foreach ($categoryResults[$category->id] as $result)
                    <div class="chart-container">
                        <!-- Representasi visual yang lebih ringkas -->
                        <div
                            style="width: 100%; margin: 15px 0; padding: 12px; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 5px;">
                            <!-- Judul pertanyaan dengan ukuran font yang lebih kecil -->
                            <div style="margin-bottom: 10px; padding-bottom: 5px; border-bottom: 1px solid #ddd;">
                                <h4 style="font-size: 12pt; margin: 0; color: #333;">{{ $result['title'] }}</h4>
                            </div>

                            <!-- Informasi ringkasan -->
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 10pt;">
                                <div>Total Responden: <strong>{{ $result['total'] }}</strong></div>
                                <div>Skor Rata-rata: <strong>{{ number_format($result['average'], 2) }}</strong></div>
                            </div>

                            <!-- Bar chart visual yang lebih ringkas -->
                            <div style="margin-bottom: 10px;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 10pt;">
                                    <!-- Sangat Baik -->
                                    <tr>
                                        <td style="width: 120px; padding: 4px 8px; text-align: left;">Sangat Baik (4)</td>
                                        <td style="width: 60px; padding: 4px 8px; text-align: right;">{{ $result['stats'][4] }}</td>
                                        <td style="width: 60px; padding: 4px 8px; text-align: right;">
                                            {{ $result['total'] > 0 ? number_format(($result['stats'][4] / $result['total']) * 100, 1) : 0 }}%</td>
                                        <td style="padding: 4px 0;">
                                            <div style="width: 100%; background-color: #e9ecef; height: 15px; border-radius: 3px; overflow: hidden;">
                                                <div
                                                    style="width: {{ $result['total'] > 0 ? ($result['stats'][4] / $result['total']) * 100 : 0 }}%; background-color: #4CAF50; height: 100%; border-radius: 3px 0 0 3px;">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Baik -->
                                    <tr>
                                        <td style="padding: 4px 8px; text-align: left;">Baik (3)</td>
                                        <td style="padding: 4px 8px; text-align: right;">{{ $result['stats'][3] }}</td>
                                        <td style="padding: 4px 8px; text-align: right;">
                                            {{ $result['total'] > 0 ? number_format(($result['stats'][3] / $result['total']) * 100, 1) : 0 }}%</td>
                                        <td style="padding: 4px 0;">
                                            <div style="width: 100%; background-color: #e9ecef; height: 15px; border-radius: 3px; overflow: hidden;">
                                                <div
                                                    style="width: {{ $result['total'] > 0 ? ($result['stats'][3] / $result['total']) * 100 : 0 }}%; background-color: #2196F3; height: 100%; border-radius: 3px 0 0 3px;">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Cukup -->
                                    <tr>
                                        <td style="padding: 4px 8px; text-align: left;">Cukup (2)</td>
                                        <td style="padding: 4px 8px; text-align: right;">{{ $result['stats'][2] }}</td>
                                        <td style="padding: 4px 8px; text-align: right;">
                                            {{ $result['total'] > 0 ? number_format(($result['stats'][2] / $result['total']) * 100, 1) : 0 }}%</td>
                                        <td style="padding: 4px 0;">
                                            <div style="width: 100%; background-color: #e9ecef; height: 15px; border-radius: 3px; overflow: hidden;">
                                                <div
                                                    style="width: {{ $result['total'] > 0 ? ($result['stats'][2] / $result['total']) * 100 : 0 }}%; background-color: #FFC107; height: 100%; border-radius: 3px 0 0 3px;">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Kurang -->
                                    <tr>
                                        <td style="padding: 4px 8px; text-align: left;">Kurang (1)</td>
                                        <td style="padding: 4px 8px; text-align: right;">{{ $result['stats'][1] }}</td>
                                        <td style="padding: 4px 8px; text-align: right;">
                                            {{ $result['total'] > 0 ? number_format(($result['stats'][1] / $result['total']) * 100, 1) : 0 }}%</td>
                                        <td style="padding: 4px 0;">
                                            <div style="width: 100%; background-color: #e9ecef; height: 15px; border-radius: 3px; overflow: hidden;">
                                                <div
                                                    style="width: {{ $result['total'] > 0 ? ($result['stats'][1] / $result['total']) * 100 : 0 }}%; background-color: #F44336; height: 100%; border-radius: 3px 0 0 3px;">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tambahkan deskripsi chart di sini -->
                    @if (isset($result['description']))
                        <div class="chart-description">
                            {{ $result['description'] }}
                        </div>
                    @endif
                @endforeach
            @else
                <p>Tidak ada data hasil untuk kategori ini.</p>
            @endif
        @endforeach

        <!-- Page Break sebelum Analisa & Pembahasan -->
        <div class="page-break"></div>

        <h2>3.2. Analisa & Pembahasan</h2>
        <p>{{ $analisa_pembahasan ??
            'Bagian ini menjelaskan hubungan antara hasil survey dengan dokumen akademik yang menjadi acuan pelaksanaan kegiatan (Renstra, Visi dan Misi, Pedoman Akademik, Buku Kurikulum, Pedoman Tugas Akhir, dll)

                                                                                                                                                                                                                                                                                                                                                                                                Dari hasil pengisian kuesioner, didapat ' .
                ($responden_count ?? '186') .
                ' responden yang merupakan ' .
                ($responden_type ?? 'mahasiswa') .
                ' Fakultas Ilmu Komputer.

                                                                                                                                                                                                                                                                                                                                                                                                Berdasaran analisis yang dilakukan, analisis menunjukkan bahwa Fakultas Ilmu Komputer memiliki performa yang sangat baik dalam semua aspek layanan. Keandalan memperoleh rata-rata skor ' .
                ($reliability_score ?? '3.26') .
                ' mencerminkan kemampuan perusahaan dalam memberikan layanan yang akurat dan konsisten, sedangkan daya tanggap memperoleh rata-rata skor ' .
                ($responsiveness_score ?? '3.22') .
                ' menunjukkan respons cepat dan efektif terhadap permintaan dan keluhan pelanggan. Kepastian memperoleh rata-rata skor ' .
                ($assurance_score ?? '3.22') .
                ' mengindikasikan tingkat kepercayaan dan keamanan yang tinggi, didukung oleh kompetensi dan kesopanan staf. Empati memperoleh rata-rata skor ' .
                ($empathy_score ?? '3.24') .
                ' menunjukkan pemahaman dan perhatian terhadap kebutuhan individu pelanggan, dan tangible memperoleh rata-rata skor ' .
                ($tangible_score ?? '3.33') .
                ' menandakan kualitas fasilitas, peralatan, dan materi komunikasi yang sangat baik, memberikan kesan positif dan mendukung keseluruhan pengalaman pelanggan.' }}
        </p>

        <!-- Tambahkan tabel ringkasan kategori -->
        <h3>Ringkasan Hasil per Kategori</h3>
        <table class="content-table">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Skor Rata-rata</th>
                    <th>Penilaian</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    @php
                        $catScore = isset($categoryStats[$category->id]) ? $categoryStats[$category->id]['average'] : 0;
                        $assessment = '';

                        if ($catScore >= 3.5) {
                            $assessment = 'Sangat Baik';
                        } elseif ($catScore >= 2.5) {
                            $assessment = 'Baik';
                        } elseif ($catScore >= 1.5) {
                            $assessment = 'Cukup';
                        } else {
                            $assessment = 'Kurang';
                        }
                    @endphp
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ number_format($catScore, 2) }}</td>
                        <td>{{ $assessment }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td><strong>Rata-rata Keseluruhan</strong></td>
                    <td><strong>{{ number_format($overallAverage, 2) }}</strong></td>
                    <td><strong>
                            @if ($overallAverage >= 3.5)
                                Sangat Baik
                            @elseif($overallAverage >= 2.5)
                                Baik
                            @elseif($overallAverage >= 1.5)
                                Cukup
                            @else
                                Kurang
                            @endif
                        </strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Bab IV: Penutup -->
    <div class="content-page" style="line-height: 1.5;">
        <div class="page-title">BAB IV PENUTUP</div>

        <h2 style="margin-bottom: 5px;">4.1. Simpulan</h2>
        <p style="margin-bottom: 8px;">
            {{ $simpulan ??
                'Berdasarkan analisis dengan jumlah responden ' .
                    ($responden_count ?? '186') .
                    ' ' .
                    ($responden_type ?? 'mahasiswa') .
                    ' yang telah dilakukan, hasil analisis menunjukkan bahwa Fakultas memiliki performa yang sangat baik dalam semua aspek layanan. Keandalan memperoleh rata-rata skor ' .
                    ($reliability_score ?? '3.26') .
                    ' mencerminkan kemampuan perusahaan dalam memberikan layanan yang akurat dan konsisten, sedangkan daya tanggap ' .
                    ($responsiveness_score ?? '3.22') .
                    ' menunjukkan respons cepat dan efektif terhadap permintaan dan keluhan pelanggan. Kepastian ' .
                    ($assurance_score ?? '3.22') .
                    ' mengindikasikan tingkat kepercayaan dan keamanan yang tinggi, didukung oleh kompetensi dan kesopanan staf. Empati ' .
                    ($empathy_score ?? '3.24') .
                    ' menunjukkan pemahaman dan perhatian terhadap kebutuhan individu pelanggan, dan tangible ' .
                    ($tangible_score ?? '3.33') .
                    ' menandakan kualitas fasilitas, peralatan, dan materi komunikasi yang sangat baik, memberikan kesan positif dan mendukung keseluruhan pengalaman pelanggan. Rata-rata ' .
                    ($average_score ?? '3.26') .
                    '. Namun masih ada beberapa hal yang masih perlu diperhatikan.' }}
        </p>

        <h2 style="margin-bottom: 5px;">4.2. Rencana Tindak Lanjut (RTL)</h2>
        <p style="margin-bottom: 8px;">
            {{ $rtl ??
                'Berisi RTL yang akan dilakukan untuk perbaikan/peningkatan Prodi. Untuk sebagian besar pertanyaan, skor yang paling sering muncul adalah score 3, menunjukkan tingkat kepuasan Baik yang dinilai dari ' .
                    ($responden_type ?? 'mahasiswa') .
                    ' adalah mutakhir terkait pokok bahasan perkuliahan. Data menunjukkan bahwa ' .
                    ($responden_type ?? 'mahasiswa') .
                    ' umumnya merasa cukup puas dengan layanan dan fasilitas yang disediakan oleh universitas, tetapi masih ada ruang untuk perbaikan. Area seperti sistem informasi, kondisi sarana pendukung, dan kecepatan akses internet dan perangkat wifi menerima penilaian yang beragam, menandakan bahwa ini mungkin merupakan area yang perlu diberikan perhatian lebih untuk peningkatan.' }}
        </p>

        <div class="signature" style="page-break-inside: avoid; margin-top: 10px;">
            <p>Jakarta, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            <p>Dekan</p>
            <div style="height: 20px;"></div>
            <p class="name">{{ $dekan_name ?? 'Prof. Dr. Ir. Supriyanto, ST., M.Sc., IPM' }}</p>
        </div>
    </div>

</body>

</html>
