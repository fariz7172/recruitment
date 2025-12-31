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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
            $table->string('applicant_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('resume_path');
            $table->string('cover_letter_path')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'screening', 'reviewed', 'shortlisted', 'rejected', 'hired'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
