<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Isi file migrasi dengan:
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('meta_nama_alumni')->nullable();
            $table->string('meta_tahun_lulus_alumni')->nullable();
            $table->string('meta_program_studi_alumni')->nullable();
            $table->string('meta_jenis_mitra')->nullable();
            $table->string('meta_jenis_kerjasama')->nullable();
            $table->string('meta_lingkup_kerjasama')->nullable();
            $table->string('meta_periode_kerjasama')->nullable();
            $table->text('meta_alamat')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'meta_nama_alumni',
                'meta_tahun_lulus_alumni',
                'meta_program_studi_alumni',
                'meta_jenis_mitra',
                'meta_jenis_kerjasama',
                'meta_lingkup_kerjasama',
                'meta_periode_kerjasama',
                'meta_alamat',
            ]);
        });
    }
};
