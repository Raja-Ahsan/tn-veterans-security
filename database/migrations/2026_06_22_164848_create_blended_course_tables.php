<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('video_url')->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('module_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_module_id')->constrained()->cascadeOnDelete();
            $table->text('question');
            $table->json('options');
            $table->string('correct_answer');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('student_module_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_module_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_completed')->default(false);
            $table->unsignedTinyInteger('best_score')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->boolean('admin_override')->default(false);
            $table->timestamps();

            $table->unique(['customer_id', 'course_module_id']);
        });

        Schema::create('module_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_module_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('score');
            $table->boolean('passed')->default(false);
            $table->json('answers')->nullable();
            $table->timestamps();
        });

        Schema::create('in_person_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_schedule_id')->nullable()->constrained()->nullOnDelete();
            $table->string('result');
            $table->text('notes')->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tested_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('in_person_test_results');
        Schema::dropIfExists('module_quiz_attempts');
        Schema::dropIfExists('student_module_progress');
        Schema::dropIfExists('module_quiz_questions');
        Schema::dropIfExists('course_modules');
    }
};
