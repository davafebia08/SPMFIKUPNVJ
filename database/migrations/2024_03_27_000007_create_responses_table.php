<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questionnaire_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('rating'); // 1-4 (Kurang - Sangat Baik)
            $table->text('comment')->nullable();
            $table->timestamps();

            // Hanya bisa mengisi sekali per pertanyaan
            $table->unique(['questionnaire_id', 'question_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responses');
    }
};
