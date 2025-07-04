<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique()->nullable(); // Untuk SSO
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['mahasiswa', 'dosen', 'tendik', 'alumni', 'pengguna_lulusan', 'mitra', 'admin', 'pimpinan'])->default('mahasiswa');
            $table->string('profile_photo')->nullable();
            $table->string('nim')->nullable()->unique(); // Untuk mahasiswa
            $table->string('nip')->nullable()->unique(); // Untuk dosen
            $table->string('nik')->nullable()->unique(); // Untuk tendik dan alumni
            $table->string('npwp')->nullable(); // Untuk alumni
            $table->string('domisili')->nullable(); // Untuk alumni
            $table->string('program_studi')->nullable(); // Tambahkan kolom program_studi
            $table->string('tahun_lulus')->nullable(); // Untuk alumni
            $table->string('tahun_angkatan')->nullable(); // Untuk alumni
            $table->string('nama_instansi')->nullable(); // Untuk mitra dan pengguna lulusan
            $table->string('jabatan')->nullable(); // Untuk pengguna lulusan dan mitra
            $table->string('no_telepon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
