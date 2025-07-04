<?php

namespace Database\Seeders;

use App\Models\QuestionnaireCategory;
use Illuminate\Database\Seeder;

class QuestionnaireCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Reliability',
                'description' => 'Kemampuan memberikan layanan sesuai dengan yang dijanjikan secara akurat dan terpercaya'
            ],
            [
                'name' => 'Responsiveness',
                'description' => 'Kesediaan untuk membantu dan memberikan layanan yang cepat (responsif)'
            ],
            [
                'name' => 'Assurance',
                'description' => 'Pengetahuan, kesopanan, dan kemampuan para staf untuk menimbulkan rasa percaya para pengguna'
            ],
            [
                'name' => 'Empathy',
                'description' => 'Kepedulian dan perhatian secara individual yang diberikan kepada pengguna'
            ],
            [
                'name' => 'Tangible',
                'description' => 'Tampilan fisik, fasilitas, peralatan, personel, dan materi komunikasi'
            ]
        ];

        foreach ($categories as $category) {
            QuestionnaireCategory::create($category);
        }
    }
}
