<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pertanyaan untuk Evaluasi Layanan Fakultas (ID: 1)
        $questions = [
            // Reliability
            ['category_id' => 1, 'question' => 'Bagaimana ketersediaan informasi yang disajikan dalam webinar fakultas?', 'order' => 1],
            ['category_id' => 1, 'question' => 'Bagaimana keterbaruan (digitalisasi) sistem pelayanan yang diberikan oleh Fakultas terhadap pelanggan?', 'order' => 2],

            // Responsiveness
            ['category_id' => 2, 'question' => 'Bagaimana respon manajemen Fakultas terhadap keluhan dari pelanggan?', 'order' => 3],
            ['category_id' => 2, 'question' => 'Bagaimana respon Fakultas terhadap saran/masukan/kritik dari pelanggan?', 'order' => 4],

            // Assurance
            ['category_id' => 3, 'question' => 'Bagaimana kemudahan pelanggan untuk mendapatkan informasi kegiatan-kegiatan Fakultas?', 'order' => 5],
            ['category_id' => 3, 'question' => 'Bagaimana manfaat kegiatan yang diadakan oleh LP3M untuk meningkatkan mutu pelanggan (Prodi, Fakultas, Lembaga, Biro, UPT)?', 'order' => 6],

            // Empathy
            ['category_id' => 4, 'question' => 'Bagaimana komunikasi antara staf Fakultas dengan pelanggan?', 'order' => 7],
            ['category_id' => 4, 'question' => 'Bagaimana kejelasan informasi yang diberikan staf Fakultas kepada pelanggan?', 'order' => 8],
            ['category_id' => 4, 'question' => 'Bagaimana keramahan dan kesopanan staf Fakultas dalam melayani pelanggan?', 'order' => 9],

            // Tangible
            ['category_id' => 5, 'question' => 'Bagaimana ketersediaan fasilitas kemudahan mencari (mengakses) kantor Fakultas?', 'order' => 10],
            ['category_id' => 5, 'question' => 'Bagaimana kenyamanan fasilitas ruang tunggu di Fakultas? (misal: tempat duduk, AC, bahan bacaan, akses wifi)', 'order' => 11],
            ['category_id' => 5, 'question' => 'Bagaimana kebersihan ruangan Fakultas?', 'order' => 12],
            ['category_id' => 5, 'question' => 'Bagaimana ketersediaan fasilitas toilet Fakultas?', 'order' => 13],
            ['category_id' => 5, 'question' => 'Bagaimana ketersediaan fasilitas kotak saran dari Fakultas?', 'order' => 14],
        ];

        foreach ($questions as $question) {
            Question::create([
                'questionnaire_id' => 1,
                'category_id' => $question['category_id'],
                'question' => $question['question'],
                'order' => $question['order'],
                'is_required' => true,
                'is_active' => true,
            ]);
        }

        // Pertanyaan untuk ELOM (ID: 2)
        $questions = [
            // Reliability
            ['category_id' => 1, 'question' => 'Bagaimana kualitas pengelolaan Fakultas dalam kegiatan KRS/KHS?', 'order' => 1],
            ['category_id' => 1, 'question' => 'Bagaimana kualitas pengelolaan Fakultas dalam proses pembelajaran (termasuk jadwal kuliah, kompetensi dosen, ketersediaan buku/ebook/hand out/modul, elearning, kualitas soal, penilaian)?', 'order' => 2],
            ['category_id' => 1, 'question' => 'Bagaimana kualitas pengelolaan Fakultas dalam pelaksanaan praktikum (termasuk penetapan dosen, jadwal, ketersediaan materi/modul, alat/bahan di lab)?', 'order' => 3],
            ['category_id' => 1, 'question' => 'Bagaimana kualitas pengelolaan Fakultas dalam mengatur jadwal ujian matakuliah, (termasuk pengumuman, time line & syarat pendaftaran)?', 'order' => 4],
            ['category_id' => 1, 'question' => 'Bagaimana kualitas pengelolaan Fakultas dalam program bid. kemahasiswaan (PKM, KBMI, Expo KMI, Lomba Akademik/Non Akademik, UKM, HMJ, BEM)?', 'order' => 5],
            ['category_id' => 1, 'question' => 'Bagaimana kejelasan informasi yang diberikan kepada mahasiswa terkait dengan kegiatan akademik dan non akademik?', 'order' => 6],
            ['category_id' => 1, 'question' => 'Bagaimana kualitas pengelolaan Fakultas dalam pelaksanaan 8 Program MBKM?', 'order' => 7],

            // Responsiveness
            ['category_id' => 2, 'question' => 'Bagaimana fakultas memfasilitasi kegiatan akademik dan nonakademik (contoh: beasiswa, konferensi nasional)?', 'order' => 8],
            ['category_id' => 2, 'question' => 'Bagaimana kemampuan semua dosen memberikan umpan balik terhadap setiap penugasan?', 'order' => 9],
            ['category_id' => 2, 'question' => 'Bagaimana kemudahan dan respon fakultas serta dosen pembimbing akademik (PA) dalam menanggapi permasalahan mahasiswa?', 'order' => 10],
            ['category_id' => 2, 'question' => 'Bagaimana kecepatan dan pelayanan yang diberikan tenaga kependidikan dalam menyiapkan sarana prasarana perkuliahan (ruang kelas, LCD, laptop, lampu, dsb)?', 'order' => 11],
            ['category_id' => 2, 'question' => 'Bagaimana respon Fakultas menindaklanjuti keluhan mahasiswa?', 'order' => 12],
            ['category_id' => 2, 'question' => 'Bagaimana kualitas layanan Fakultas dalam pemberian pelayanan dengan adil dan tidak membeda-bedakan mahasiswa?', 'order' => 13],

            // Assurance
            ['category_id' => 3, 'question' => 'Bagaimana kualitas pengelolaan Fakultas dalam Pembagian Ijazah?', 'order' => 14],
            ['category_id' => 3, 'question' => 'Bagaimana konsistensi Fakultas menjalankan kebijakan akademik dan non akademik yang telah ditetapkan?', 'order' => 15],
            ['category_id' => 3, 'question' => 'Bagaimana tenaga kependidikan memberikan layanan sesuai dengan kebutuhan mahasiwa?', 'order' => 16],
            ['category_id' => 3, 'question' => 'Bagaimana kualitas pengelolaan Fakultas dalam Proses Wisuda?', 'order' => 17],
            ['category_id' => 3, 'question' => 'Bagaimana kualitas pengelolaan Fakultas dalam menetapkan Proses Yudisium?', 'order' => 18],
            ['category_id' => 3, 'question' => 'Bagaimana pelayanan bidang keuangan yang diberikan oleh semua tenaga kependidikan?', 'order' => 19],

            // Empathy
            ['category_id' => 4, 'question' => 'Bagaimana semua tenaga kependidikan memberikan pelayanan tanpa membeda-bedakan mahasiswa?', 'order' => 20],
            ['category_id' => 4, 'question' => 'Bagaimana Fakultas memberikan pelayanan dengan adil dan tidak membeda bedakan mahasiswa?', 'order' => 21],
            ['category_id' => 4, 'question' => 'Bagaimana apresiasi PS/Fakultas terhadap prestasi akademik/non akademik yang diperoleh mahasiswa?', 'order' => 22],
            ['category_id' => 4, 'question' => 'Bagaimana kualitas pengelolaan Fakultas dalam memfasilitasi perolehan setifikasi kompetensi?', 'order' => 23],
            ['category_id' => 4, 'question' => 'Bagaimana kualitas layanan Fakultas dalam memfasilitasi peluang beasiswa?', 'order' => 24],

            // Tangible
            ['category_id' => 5, 'question' => 'Bagaimana kelengkapan media pembelajaran untuk penyampaian materi kuliah (termasuk LCD, Video Tutor, Elearning)?', 'order' => 25],
            ['category_id' => 5, 'question' => 'Bagaimana kualitas sarana perkuliahan (termasuk kursi, meja, AC, papan tulis, wifi)?', 'order' => 26],
            ['category_id' => 5, 'question' => 'Bagaimana keramahan, kecekatan, dan penampilan tenaga kependidikan dalam memberikan layanan?', 'order' => 27],
            ['category_id' => 5, 'question' => 'Bagaimana Fakultas menyediakan fasilitas pendidikan dan fasilitas umum?', 'order' => 28],
            ['category_id' => 5, 'question' => 'Bagaimana kelengkapan buku referensi di perpustakaan dan sarana ruang baca (jika ada)?', 'order' => 29],
            ['category_id' => 5, 'question' => 'Bagaimana kecepatan akses internet di lingkungan kampus?', 'order' => 30],
            ['category_id' => 5, 'question' => 'Bagaimana kinerja sistim informasi yang tersedia saat ini (termasuk SIAKAD, SIM Wisuda, SIM Presma, atau SIM lain yang tersedia)?', 'order' => 31],
            ['category_id' => 5, 'question' => 'Bagaimana kondisi sarana pendukung lainnya (termasuk kebersihan toilet/kelas, kualitas kantin, keamanan parkir, ketersediaan sarana ibadah)?', 'order' => 32],
        ];

        foreach ($questions as $question) {
            Question::create([
                'questionnaire_id' => 2,
                'category_id' => $question['category_id'],
                'question' => $question['question'],
                'order' => $question['order'],
                'is_required' => true,
                'is_active' => true,
            ]);
        }

        // Pertanyaan untuk Evaluasi Dosen oleh Mahasiswa (ID: 3)
        $questions = [
            // Reliability
            ['category_id' => 1, 'question' => 'Bagaimana penguasaan Dosen terhadap materi perkuliahan?', 'order' => 1],
            ['category_id' => 1, 'question' => 'Bagaimana kemampuan Dosen dalam menjelaskan materi kuliah dan menjawab pertanyaan mahasiswa secara benar dan mudah dipahami?', 'order' => 2],
            ['category_id' => 1, 'question' => 'Bagaimana kemampuan Dosen dalam memberikan contoh/ilustrasi/pengalaman yang relevan atas materi yang diberikan?', 'order' => 3],
            ['category_id' => 1, 'question' => 'Bagaimana kemampuan Dosen dalam menjelaskan keterkaitan antar topik perkuliahan dan mengaitkannya dengan kehidupan sehari-hari?', 'order' => 4],
            ['category_id' => 1, 'question' => 'Bagaimana Dosen menyampaikan buku rujukan dan sumber belajar online yang harus dipelajari?', 'order' => 5],

            // Responsiveness
            ['category_id' => 2, 'question' => 'Bagaimana metode pembelajaran yang digunakan oleh Dosen dalam menyampaikan materi?', 'order' => 6],
            ['category_id' => 2, 'question' => 'Bagaimana kemampuan Dosen dalam mengelola pembelajaran online/daring secara efektif dan interaktif?', 'order' => 7],
            ['category_id' => 2, 'question' => 'Bagaimana kemampuan Dosen dalam mengendalikan diri dalam berbagai situasi dan kondisi?', 'order' => 8],
            ['category_id' => 2, 'question' => 'Bagaimana kemampuan Dosen dalam menghidupkan suasana kelas?', 'order' => 9],
            ['category_id' => 2, 'question' => 'Bagaimana kemampuan Dosen terhadap isu-isu mutakhir terkait pokok bahasan perkuliahan?', 'order' => 10],

            // Assurance
            ['category_id' => 3, 'question' => 'Bagaimana distribusi silabus kepada mahasiswa?', 'order' => 11],
            ['category_id' => 3, 'question' => 'Bagaimana kesesuaian materi kuliah dengan silabus?', 'order' => 12],
            ['category_id' => 3, 'question' => 'Bagaimana Dosen memberikan umpan balik terhadap tugas/kuis/ujian?', 'order' => 13],
            ['category_id' => 3, 'question' => 'Bagaimana ketepatan waktu masuk dan keluar kelas?', 'order' => 14],

            // Empathy
            ['category_id' => 4, 'question' => 'Bagaimana Dosen menumbuhkan semangat belajar mandiri?', 'order' => 15],
            ['category_id' => 4, 'question' => 'Bagaimana Dosen memberikan kesempatan bertanya dan berdiskusi?', 'order' => 16],
            ['category_id' => 4, 'question' => 'Bagaimana Dosen mengenal dengan baik mahasiswa yang mengikuti kuliahnya?', 'order' => 17],

            // Tangible
            ['category_id' => 5, 'question' => 'Bagaimana kejelasan artikulasi dan volume suara Dosen saat memberikan materi kuliah?', 'order' => 18],
            ['category_id' => 5, 'question' => 'Bagaimana keragaman alat bantu yang digunakan dosen untuk proses pembelajaran?', 'order' => 19],
            ['category_id' => 5, 'question' => 'Bagaimana kerapihan dan kesopanan Dosen berpakaian?', 'order' => 20],
        ];

        foreach ($questions as $question) {
            Question::create([
                'questionnaire_id' => 3,
                'category_id' => $question['category_id'],
                'question' => $question['question'],
                'order' => $question['order'],
                'is_required' => true,
                'is_active' => true,
            ]);
        }

        // Pertanyaan untuk Evaluasi Layanan Tugas Akhir (ELTA) (ID: 4)
        $questions = [
            // Reliability
            ['category_id' => 1, 'question' => 'Bagaimana kompetensi dosen pembimbing tugas akhir?', 'order' => 1],
            ['category_id' => 1, 'question' => 'Bagaimana kompetensi dosen penguji tugas akhir?', 'order' => 2],
            ['category_id' => 1, 'question' => 'Bagaimana kualitas pengelolaan program studi dalam menetapkan Dosen Pembimbing Tugas Akhir?', 'order' => 3],
            ['category_id' => 1, 'question' => 'Bagaimana kualitas pengelolaan program studi dalam mengatur jadwal seminar proposal, atau sidang tugas akhir (termasuk pengumuman, time line & syarat pendaftaran)?', 'order' => 4],
            ['category_id' => 1, 'question' => 'Bagaimana kejelasan prosedur dalam pembimbingan tugas akhir yang tercantum di buku manual prosedur?', 'order' => 5],
            ['category_id' => 1, 'question' => 'Bagaimana kejelasan tata cara penulisan naskah tugas akhir yang tercantum di buku pedoman penulisan tugas akhir?', 'order' => 6],

            // Responsiveness
            ['category_id' => 2, 'question' => 'Bagaimana kemudahan dan respon dosen pembimbing dalam menanggapi keluhan mahasiswa ketika mengalami kesulitan dalam menyelesaikan tugas akhir?', 'order' => 7],
            ['category_id' => 2, 'question' => 'Bagaimana kemudahan dan respon ketua program studi dalam menanggapi keluhan mahasiswa ketika mengalami kesulitan dalam menyelesaikan tugas akhir?', 'order' => 8],
            ['category_id' => 2, 'question' => 'Bagaimana kemudahan dan respon tenaga kependidikan dalam menanggapi pertanyaan mahasiswa ketika mengalami kesulitan dalam menyelesaikan tugas akhir?', 'order' => 9],

            // Assurance
            ['category_id' => 3, 'question' => 'Bagaimana konsistensi program studi menjalankan manual prosedur tugas akhir yang telah ditetapkan?', 'order' => 10],
            ['category_id' => 3, 'question' => 'Bagaimana konsistensi dosen menjalankan manual prosedur tugas akhir yang telah ditetapkan?', 'order' => 11],
            ['category_id' => 3, 'question' => 'Bagaimana konsistensi program studi melaksanakan buku pedoman penulisan tugas akhir?', 'order' => 12],
            ['category_id' => 3, 'question' => 'Bagaimana konsistensi dosen melaksanakan buku pedoman penulisan tugas akhir?', 'order' => 13],

            // Empathy
            ['category_id' => 4, 'question' => 'Bagaimana pelayanan semua tenaga kependidikan tanpa membeda-bedakan mahasiswa?', 'order' => 14],
            ['category_id' => 4, 'question' => 'Bagaimana pelayanan semua tenaga kependidikan dalam menyediakan dokumen tugas akhir (termasuk surat pengantar, berita acara)?', 'order' => 15],
            ['category_id' => 4, 'question' => 'Bagaimana pelayanan petugas perpustakaan dalam menerima dokumen tugas akhir (termasuk menyiapkan surat bebas pustaka)?', 'order' => 16],

            // Tangible
            ['category_id' => 5, 'question' => 'Bagaimana kinerja sistim informasi yang digunakan untuk pengelolaan tugas akhir mahasiswa?', 'order' => 17],
            ['category_id' => 5, 'question' => 'Bagaimana akses terhadap repository tugas akhir di perpustakaan?', 'order' => 18],
        ];

        foreach ($questions as $question) {
            Question::create([
                'questionnaire_id' => 4,
                'category_id' => $question['category_id'],
                'question' => $question['question'],
                'order' => $question['order'],
                'is_required' => true,
                'is_active' => true,
            ]);
        }

        // Pertanyaan untuk Kepuasan Dosen (ID: 5)
        $questions = [
            // Reliability
            ['category_id' => 1, 'question' => 'Bagaimana penempatan/ploting dosen mengajar di UPNVJ?', 'order' => 1],
            ['category_id' => 1, 'question' => 'Bagaimana pemberdayaan dosen untuk menjadi pembimbing akademik mahasiswa?', 'order' => 2],
            ['category_id' => 1, 'question' => 'Bagaimana kesempatan yang diberikan kepada dosen untuk menyusun buku ajar atau handout, modul, atau karya ilmiah yang lainnya?', 'order' => 3],

            // Responsiveness
            ['category_id' => 2, 'question' => 'Bagaimana dukungan fasilitas yang diterima dosen untuk mengurus kepangkatan akademik?', 'order' => 4],
            ['category_id' => 2, 'question' => 'Bagaimana kesempatan yang diberikan kepada dosen untuk mengikuti kegiatan seminar/pertemuan ilmiah sesuai bidang ilmu?', 'order' => 5],
            ['category_id' => 2, 'question' => 'Bagaimana kesempatan yang diberikan kepada dosen untuk melanjutkan studi ke jenjang yang lebih tinggi dengan bantuan pembiayaan dari internal UPN Veteran Jakarta atau pihak eksternal?', 'order' => 6],
            ['category_id' => 2, 'question' => 'Bagaimana kesempatan yang diberikan kepada dosen untuk meningkatkan jenjang karir sesuai dengan aturan yang jelas?', 'order' => 7],
            ['category_id' => 2, 'question' => 'Bagaimana dosen mendapatkan manfaat dari hasil kerjasama UPN Veteran Jakarta dengan pihak eksternal?', 'order' => 8],
            ['category_id' => 2, 'question' => 'Bagaimana koordinasi/kerjasama antar lembaga/fakultas/program studi/unit di lingkungan UPN Veteran Jakarta?', 'order' => 9],

            // Assurance
            ['category_id' => 3, 'question' => 'Bagaimana pemenuhan minimal sks (12 sks) bagi dosen setiap semester?', 'order' => 10],
            ['category_id' => 3, 'question' => 'Bagaimana perihal kewajiban dosen untuk membuat silabus, SAP (Satuan Acara Perkuliahan), dan BAP (Berita Acara Perkuliahan) sesuai dengan mata kuliah yang diampunya?', 'order' => 11],
            ['category_id' => 3, 'question' => 'Bagaimana perihal dosen untuk melakukan evaluasi pembelajaran secara objektif dan transparan?', 'order' => 12],
            ['category_id' => 3, 'question' => 'Bagaimana perihal gaji dan pendapatan lain yang diterima dosen untuk memenuhi kebutuhan hidup yang memadai?', 'order' => 13],
            ['category_id' => 3, 'question' => 'Bagaimana kesesuaian THR yang diperoleh Dosen dengan aturan yang berlaku?', 'order' => 14],
            ['category_id' => 3, 'question' => 'Bagaimana kesesuaian jaminan kesehatan yang diterima oleh dosen dengan aturan yang berlaku?', 'order' => 15],

            // Empathy
            ['category_id' => 4, 'question' => 'Bagaimana informasi yang disampaikan kepada dosen untuk melakukan penelitian internal maupun eksternal?', 'order' => 16],
            ['category_id' => 4, 'question' => 'Bagaimana kesempatan yang disampaikan kepada dosen untuk melakukan penelitian sesuai keahlian sekali dalam satu semester baik didanai oleh internal UMK atau pihak eksternal?', 'order' => 17],
            ['category_id' => 4, 'question' => 'Bagaimana informasi yang disampaikan kepada dosen untuk melakukan pengabdian masyarakat dengan sumber pendanaan baik internal maupun eksternal?', 'order' => 18],
            ['category_id' => 4, 'question' => 'Bagaimana kesempatan yang disampaikan kepada dosen untuk melakukan pengabdian masyarakat sesuai keahlian sekali dalam satu semester baik didanai oleh internal UMK atau pihak eksternal?', 'order' => 19],
            ['category_id' => 4, 'question' => 'Bagaimana perhatian yang diberikan Pimpinan untuk setiap hasil kerja dosen?', 'order' => 20],
            ['category_id' => 4, 'question' => 'Bagaimana Pimpinan berkomunikasi dengan dosen?', 'order' => 21],
            ['category_id' => 4, 'question' => 'Bagaimana dukungan yang diberikan pimpinan pada setiap pekerjaan dosen?', 'order' => 22],
            ['category_id' => 4, 'question' => 'Bagaimana reward dan punishment yang diberikan Pimpinan terhadap hasil kerja dosen?', 'order' => 23],
            ['category_id' => 4, 'question' => 'Bagaimana pengawasan yang dilakukan Pimpinan terhadap tugas dosen dalam mengajar?', 'order' => 24],
            ['category_id' => 4, 'question' => 'Bagaimana kesempatan yang diberikan kepada dosen untuk mengikuti seleksi dosen teladan setiap tahun?', 'order' => 25],
            ['category_id' => 4, 'question' => 'Bagaimana bantuan yang diterima dosen apabila mendapat musibah yang menimpa diri dosen dan atau keluarga?', 'order' => 26],
            ['category_id' => 4, 'question' => 'Bagaimana dosen mendapatkan penyelesaian jika mengalami permasalahan atau konflik yang berkaitan dengan pekerjaan?', 'order' => 27],

            // Tangible
            ['category_id' => 5, 'question' => 'Bagaimana penyediaan LCD di setiap ruang kelas untuk proses pembelajaran?', 'order' => 28],
            ['category_id' => 5, 'question' => 'Bagaimana kenyamanan ruang kelas untuk proses pembelajaran?', 'order' => 29],
            ['category_id' => 5, 'question' => 'Bagaimana ketersediaan ruangan yang nyaman untuk ruang kerja dosen dalam melayani mahasiswa (PA dan bimbingan skripsi)?', 'order' => 30],
            ['category_id' => 5, 'question' => 'Bagaimana ketersediaan sarana akses internet bagi dosen untuk memperlancar tugas-tugasnya?', 'order' => 31],
            ['category_id' => 5, 'question' => 'Bagaimana ketersediaan toilet yang bersih dan memadai?', 'order' => 32],
            ['category_id' => 5, 'question' => 'Bagaimana ketersediaan sarana laboratorium yang memadai untuk menunjang proses pembelajaran?', 'order' => 33],
            ['category_id' => 5, 'question' => 'Bagaimana ketersediaan sarana perpustakaan yang memadai dengan koleksi pustaka yang representatif?', 'order' => 34],
            ['category_id' => 5, 'question' => 'Bagaimana ketersediaan sarana dan fasilitas kesehatan?', 'order' => 35],
            ['category_id' => 5, 'question' => 'Bagaimana ketersediaan sarana koperasi untuk memenuhi kebutuhan sehari-hari dosen termasuk simpan pinjam?', 'order' => 36],
            ['category_id' => 5, 'question' => 'Bagaimana ketersediaan sarana parkir yang memadai dan aman?', 'order' => 37],
            ['category_id' => 5, 'question' => 'Bagaimana ketersediaan penerangan yang memadai di semua ruangan?', 'order' => 38],
        ];

        foreach ($questions as $question) {
            Question::create([
                'questionnaire_id' => 5,
                'category_id' => $question['category_id'],
                'question' => $question['question'],
                'order' => $question['order'],
                'is_required' => true,
                'is_active' => true,
            ]);
        }

        // Pertanyaan untuk Kepuasan Tenaga Kependidikan (ID: 6)
        $questions = [
            // Reliability
            ['category_id' => 1, 'question' => 'Bagaimana kesesuaian kompetensi dengan penempatan tenaga kependidikan di UPNVJ?', 'order' => 1],
            ['category_id' => 1, 'question' => 'Bagaimana kesesuaian kompetensi dengan latar belakang pendidikan tenaga kependidikan di UPNVJ?', 'order' => 2],
            ['category_id' => 1, 'question' => 'Bagaimana kesesuaian kemampuan dengan penempatan tenaga kependidikan di UPNVJ?', 'order' => 3],

            // Responsiveness
            ['category_id' => 2, 'question' => 'Bagaimana keterbukaan managemen pengelola memberikan informasi dan berkomunikasi kepada tenaga kependidikan?', 'order' => 4],
            ['category_id' => 2, 'question' => 'Bagaimana managemen pengelola memberikan promosi jabatan berdasarkan hasil kinerja tenaga kependidikan?', 'order' => 5],
            ['category_id' => 2, 'question' => 'Bagaimana managemen pengelola memberikan pelatihan dalam peningkatan pelayanan tenaga kependidikan?', 'order' => 6],

            // Assurance
            ['category_id' => 3, 'question' => 'Bagaimana managemen pengelola mengembangkan kemampuan tenaga kependidikan melalui pelatihan/workshop?', 'order' => 7],
            ['category_id' => 3, 'question' => 'Bagaimana managemen pengelola menerapkan sistem jenjang karir bagi tenaga kependidikan?', 'order' => 8],
            ['category_id' => 3, 'question' => 'Bagaimana managemen pengelola menerapkan sistem pengembangan kemampuan dan kinerja tenaga kependidikan melalui rotasi/mutasi di UPNVJ?', 'order' => 9],
            ['category_id' => 3, 'question' => 'Bagaimana managemen pengelola menerapkan sistem pengembangan karier tenaga kependidikan melalui peningkatan kesejahteraan yang memadai?', 'order' => 10],

            // Empathy
            ['category_id' => 4, 'question' => 'Bagaimana atasan dalam membagi pekerjaan kepada tenaga kependidikan secara proporsional?', 'order' => 11],
            ['category_id' => 4, 'question' => 'Bagaimana atasan dalam memotivasi kerjasama dengan tenaga kependidikan?', 'order' => 12],
            ['category_id' => 4, 'question' => 'Bagaimana atasan dalam memberikan pengarahan kepada tenaga kependidikan dalam melakukan tugasnya?', 'order' => 13],
            ['category_id' => 4, 'question' => 'Bagaimana UPNVJ memberikan penghargaan (hadiah/pujian) terhadap hasil kerja tenaga kependidikan yang baik?', 'order' => 14],
            ['category_id' => 4, 'question' => 'Bagaimana atasan dalam melakukan pengawasan terhadap kinerja tenaga kependidikan?', 'order' => 15],
            ['category_id' => 4, 'question' => 'Bagaimana UPNVJ dalam menyediakan program JHT bagi pegawai?', 'order' => 16],
            ['category_id' => 4, 'question' => 'Bagaimana UPNVJ dalam memberikan jaminan kesehatan (BPJS)?', 'order' => 17],
            ['category_id' => 4, 'question' => 'Bagaimana UPNVJ dalam memberikan THR yang memadai?', 'order' => 18],

            // Tangible
            ['category_id' => 5, 'question' => 'Bagaimana UPNVJ dalam memberikan penyediaan ruang kerja yang nyaman?', 'order' => 19],
            ['category_id' => 5, 'question' => 'Bagaimana UPNVJ dalam memberikan penyediaan kelengkapan kerja (komputer, printer, ATK) sebagai penunjang kinerja tenaga kependidikan?', 'order' => 20],
            ['category_id' => 5, 'question' => 'Bagaimana UPNVJ dalam memberikan penyediaan sistem informasi (telepon, internet, email)?', 'order' => 21],
        ];

        foreach ($questions as $question) {
            Question::create([
                'questionnaire_id' => 6,
                'category_id' => $question['category_id'],
                'question' => $question['question'],
                'order' => $question['order'],
                'is_required' => true,
                'is_active' => true,
            ]);
        }

        // Pertanyaan untuk Kepuasan Alumni (ID: 7)
        $questions = [
            // Reliability
            ['category_id' => 1, 'question' => 'Bagaimana kecakapan tenaga kependidikan dalam memberikan pelayanan?', 'order' => 1],
            ['category_id' => 1, 'question' => 'Bagaimana Fakultas memfasilitasi kebutuhan lulusan seperti surat keterangan, transkrip nilai, ijazah, tracer study?', 'order' => 2],
            ['category_id' => 1, 'question' => 'Bagaimana keaktifan Fakultas untuk membangun kerjasama dengan alumni seperti pengembangan kurikulum, pengembangan prodi, dan pengembangan sarana prasana?', 'order' => 3],
            ['category_id' => 1, 'question' => 'Bagaimana keaktifan Fakultas untuk membangun kerjasama dengan berbagai Lembaga baik Lembaga Pendidikan maupun non Pendidikan baik swasta maupun pemerintah?', 'order' => 4],

            // Responsiveness
            ['category_id' => 2, 'question' => 'Bagaimana kecepatan tenaga kependidikan untuk menyiapkan sarana dan layanan?', 'order' => 5],
            ['category_id' => 2, 'question' => 'Bagaimana kecepatan Fakultas untuk menindaklanjuti keluhan lulusan?', 'order' => 6],

            // Assurance
            ['category_id' => 3, 'question' => 'Bagaimana tenaga kependidikan memberikan layanan yang sesuai dengan kebutuhan lulusan?', 'order' => 7],
            ['category_id' => 3, 'question' => 'Bagaimana Fakultas menetapkan kebijakan akademik dan non akademik secara konsisten?', 'order' => 8],

            // Empathy
            ['category_id' => 4, 'question' => 'Bagaimana sikap dosen dalam menerima kritik, saran, dan memberikan kesempatan bertanya kepada mahasiswa secara adil?', 'order' => 9],
            ['category_id' => 4, 'question' => 'Bagaimana tenaga kependidikan memberikan pelayanan kepada mahasiswa tanpa membeda bedakan?', 'order' => 10],
            ['category_id' => 4, 'question' => 'Bagaimana Fakultas memberikan pelayanan kepada mahasiswa dengan adil dan tidak membedakan mahasiswa?', 'order' => 11],

            // Tangible
            ['category_id' => 5, 'question' => 'Bagaimana penggunaan media pembelajaran dalam penyampaian materi kuliah?', 'order' => 12],
            ['category_id' => 5, 'question' => 'Bagaimana keramahan, kesopanan, dan penampilan tenaga kependidikan dalam memberikan layanan?', 'order' => 13],
        ];

        foreach ($questions as $question) {
            Question::create([
                'questionnaire_id' => 7,
                'category_id' => $question['category_id'],
                'question' => $question['question'],
                'order' => $question['order'],
                'is_required' => true,
                'is_active' => true,
            ]);
        }

        // Pertanyaan untuk Kepuasan Pengguna Lulusan (ID: 8)
        $questions = [
            // Reliability
            ['category_id' => 1, 'question' => 'Bagaimana keahlian lulusan UPNVJ berdasarkan bidang ilmu yang dimiliki?', 'order' => 1],
            ['category_id' => 1, 'question' => 'Bagaimana kemampuan berbahasa Inggris atau bahasa asing lulusan UPNVJ?', 'order' => 2],
            ['category_id' => 1, 'question' => 'Bagaimana kemampuan lulusan UPNVJ menggunakan Teknologi dan Informasi?', 'order' => 3],

            // Responsiveness
            ['category_id' => 2, 'question' => 'Bagaimana kemampuan lulusan UPNVJ mengembangkan potensi diri?', 'order' => 4],
            ['category_id' => 2, 'question' => 'Bagaimana kedisiplinan dan loyalitas alumni UPNVJ?', 'order' => 5],

            // Assurance
            ['category_id' => 3, 'question' => 'Bagaimana Integritas (Etika dan Moral) lulusan UPNVJ di Perusahaan anda?', 'order' => 6],

            // Empathy
            ['category_id' => 4, 'question' => 'Bagaimana kerjasama lulusan UPNVJ dalam Tim Kerja?', 'order' => 7],

            // Tangible
            ['category_id' => 5, 'question' => 'Bagaimana kemampuan komunikasi lulusan UPNVJ?', 'order' => 8],
        ];

        foreach ($questions as $question) {
            Question::create([
                'questionnaire_id' => 8,
                'category_id' => $question['category_id'],
                'question' => $question['question'],
                'order' => $question['order'],
                'is_required' => true,
                'is_active' => true,
            ]);
        }

        // Pertanyaan untuk Kepuasan Mitra Kerjasama (ID: 9)
        $questions = [
            // Reliability
            ['category_id' => 1, 'question' => 'Bagaimana ketepatan waktu dalam proses pembuatan kerjasama (PKS) atau nota kesepahaman dengan UPNVJ?', 'order' => 1],
            ['category_id' => 1, 'question' => 'Bagaimana pemenuhan harapan dari hasil kerjasama dengan UPNVJ?', 'order' => 2],

            // Responsiveness
            ['category_id' => 2, 'question' => 'Bagaimana keandalan dan profesionalisme staf UPNVJ yang terlibat dalam kegiatan kerjasama selama ini?', 'order' => 3],
            ['category_id' => 2, 'question' => 'Bagaimana UPNVJ mengkomunikasikan laporan hasil kerjasama selama ini?', 'order' => 4],

            // Assurance
            ['category_id' => 3, 'question' => 'Bagaimana implementasi kegiatan kerjasama atas PKS/MoU yang telah disepakati?', 'order' => 5],
            ['category_id' => 3, 'question' => 'Bagaimana keberlanjutan kerjasama antara Institusi kami dengan UPNVJ di masa mendatang?', 'order' => 6],

            // Empathy
            ['category_id' => 4, 'question' => 'Bagaimana pendampingan dan bantuan yang diberikan pihak UPNVJ dalam pelaksanaan kegiatan kerjasama?', 'order' => 7],
            ['category_id' => 4, 'question' => 'Bagaimana manfaat yang dirasakan institusi selama kegiatan kerjasama berlangsung?', 'order' => 8],

            // Tangible
            ['category_id' => 5, 'question' => 'Bagaimana ketersediaan sarana dan prasarana yang digunakan dalam pelaksanaan kegiatan kerjasama?', 'order' => 9],
            ['category_id' => 5, 'question' => 'Bagaimana keramahan dan kesopanan yang ditunjukan oleh staf UPNVJ selama kegiatan kerjasama ini?', 'order' => 10],
        ];

        foreach ($questions as $question) {
            Question::create([
                'questionnaire_id' => 9,
                'category_id' => $question['category_id'],
                'question' => $question['question'],
                'order' => $question['order'],
                'is_required' => true,
                'is_active' => true,
            ]);
        }
    }
}
