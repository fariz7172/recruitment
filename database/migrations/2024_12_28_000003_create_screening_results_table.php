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
        Schema::create('screening_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->json('extracted_data')->nullable();
            $table->integer('score')->default(0);
            $table->enum('recommendation', ['SANGAT_SESUAI', 'SESUAI', 'PERTIMBANGKAN', 'TIDAK_SESUAI'])->nullable();
            $table->json('skill_match')->nullable();
            $table->text('ai_analysis')->nullable();
            $table->text('ai_summary')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screening_results');
    }
};
