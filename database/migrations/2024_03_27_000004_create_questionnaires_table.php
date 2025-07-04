<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaires', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Judul kuesioner
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', [
                'layanan_fakultas',
                'elom',
                'evaluasi_dosen',
                'elta',
                'kepuasan_dosen',
                'kepuasan_tendik',
                'kepuasan_alumni',
                'kepuasan_pengguna_lulusan',
                'kepuasan_mitra'
            ]);
            $table->foreignId('academic_period_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaires');
    }
};
