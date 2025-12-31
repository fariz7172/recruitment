<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psychometric_tests', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "DISC Personality Test"
            $table->string('slug')->unique(); // e.g., "disc"
            $table->text('description')->nullable();
            $table->enum('type', ['personality', 'cognitive', 'aptitude'])->default('personality');
            $table->integer('duration_minutes')->default(30);
            $table->integer('total_questions')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // Store additional settings
            $table->timestamps();
        });

        Schema::create('psychometric_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('psychometric_tests')->onDelete('cascade');
            $table->text('question_text');
            $table->enum('question_type', ['likert', 'multiple_choice', 'forced_choice'])->default('likert');
            $table->json('options'); // Answer options
            $table->string('dimension')->nullable(); // e.g., "D", "I", "S", "C" for DISC
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('psychometric_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->foreignId('test_id')->constrained('psychometric_tests')->onDelete('cascade');
            $table->json('answers'); // All answers
            $table->json('scores'); // Calculated scores per dimension
            $table->string('primary_type')->nullable(); // e.g., "D" for DISC
            $table->string('secondary_type')->nullable();
            $table->text('analysis')->nullable(); // AI-generated analysis
            $table->integer('completion_time_seconds')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['application_id', 'test_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psychometric_results');
        Schema::dropIfExists('psychometric_questions');
        Schema::dropIfExists('psychometric_tests');
    }
};
