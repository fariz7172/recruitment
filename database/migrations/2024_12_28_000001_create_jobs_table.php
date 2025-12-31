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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->enum('employment_type', ['full-time', 'part-time', 'contract', 'internship'])->default('full-time');
            $table->string('min_education');
            $table->integer('min_experience_years')->default(0);
            $table->json('required_skills');
            $table->json('preferred_skills')->nullable();
            $table->decimal('salary_min', 15, 2)->nullable();
            $table->decimal('salary_max', 15, 2)->nullable();
            $table->date('deadline')->nullable();
            $table->enum('status', ['active', 'closed', 'draft'])->default('active');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
