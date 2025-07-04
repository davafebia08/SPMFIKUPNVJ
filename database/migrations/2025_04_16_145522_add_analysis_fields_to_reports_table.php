<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->text('analysis_content')->nullable()->after('notes');
            $table->text('conclusion_content')->nullable()->after('analysis_content');
            $table->text('followup_content')->nullable()->after('conclusion_content');
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['analysis_content', 'conclusion_content', 'followup_content']);
        });
    }
};
