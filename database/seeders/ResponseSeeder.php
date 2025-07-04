<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\Response;
use App\Models\User;
use Illuminate\Database\Seeder;

class ResponseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua questionnaire
        $questionnaires = Questionnaire::all();

        foreach ($questionnaires as $questionnaire) {
            // Tentukan tingkat kepuasan berdasarkan tipe kuesioner
            $satisfactionLevel = $this->getSatisfactionLevel($questionnaire->type);

            // Tentukan role yang berhak mengisi kuesioner ini
            $allowedRoles = $questionnaire->permissions()
                ->where('can_fill', true)
                ->pluck('role')
                ->toArray();

            // Ambil user dengan role yang sesuai
            $users = User::whereIn('role', $allowedRoles)->get();

            // Ambil semua pertanyaan untuk kuesioner ini
            $questions = $questionnaire->questions()->where('is_active', true)->get();

            // Buat jawaban untuk setiap user
            foreach ($users as $user) {
                // Catat user telah mengisi kuesioner
                $questionnaire->users()->attach($user->id, [
                    'submitted_at' => now()->subDays(rand(1, 30))
                ]);

                // Buat jawaban untuk setiap pertanyaan
                foreach ($questions as $question) {
                    Response::create([
                        'questionnaire_id' => $questionnaire->id,
                        'question_id' => $question->id,
                        'user_id' => $user->id,
                        'rating' => $this->generateRating($satisfactionLevel),
                        'comment' => rand(1, 5) === 1 ? "Komentar dari {$user->name} untuk pertanyaan ini." : null,
                    ]);
                }
            }
        }
    }

    /**
     * Menentukan tingkat kepuasan berdasarkan tipe kuesioner
     */
    private function getSatisfactionLevel($questionnaireType)
    {
        // 4 kuesioner hasil "Sangat Baik"
        if (in_array($questionnaireType, ['layanan_fakultas', 'elta', 'evaluasi_dosen', 'kepuasan_mitra'])) {
            return 'sangat_baik';
        }

        // 1 kuesioner hasil "Cukup"
        else if ($questionnaireType === 'kepuasan_alumni') {
            return 'cukup';
        }

        // 4 kuesioner lainnya hasil "Baik"
        else {
            return 'baik';
        }
    }

    /**
     * Menghasilkan rating berdasarkan tingkat kepuasan
     */
    private function generateRating($satisfactionLevel)
    {
        switch ($satisfactionLevel) {
            case 'sangat_baik':
                // 70% rating 4, 25% rating 3, 5% rating 2
                $rand = rand(1, 100);
                if ($rand <= 70) return 4;
                if ($rand <= 95) return 3;
                return 2;

            case 'baik':
                // 30% rating 4, 60% rating 3, 10% rating 2
                $rand = rand(1, 100);
                if ($rand <= 30) return 4;
                if ($rand <= 90) return 3;
                return 2;

            case 'cukup':
                // 10% rating 4, 30% rating 3, 50% rating 2, 10% rating 1
                $rand = rand(1, 100);
                if ($rand <= 10) return 4;
                if ($rand <= 40) return 3;
                if ($rand <= 90) return 2;
                return 1;

            default:
                return rand(1, 4);
        }
    }
}
