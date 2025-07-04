<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questionnaire_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['mahasiswa', 'dosen', 'tendik', 'alumni', 'pengguna_lulusan', 'mitra', 'admin', 'pimpinan']);
            $table->boolean('can_fill')->default(true);
            $table->boolean('can_view_results')->default(false);
            $table->timestamps();

            // Unique together
            $table->unique(['questionnaire_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_permissions');
    }
};
