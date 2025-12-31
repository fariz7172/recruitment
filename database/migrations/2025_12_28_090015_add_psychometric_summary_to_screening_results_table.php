<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('screening_results', function (Blueprint $table) {
            $table->text('psychometric_summary')->nullable()->after('ai_summary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('screening_results', function (Blueprint $table) {
            $table->dropColumn('psychometric_summary');
        });
    }
};
