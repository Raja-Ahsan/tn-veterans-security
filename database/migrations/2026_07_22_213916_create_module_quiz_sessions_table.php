<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_quiz_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('course_module_id')->constrained('course_modules')->cascadeOnDelete();
            $table->unsignedInteger('current_index')->default(0);
            $table->json('answers')->nullable();
            $table->dateTime('started_at');
            $table->dateTime('expires_at');
            $table->string('status', 20)->default('in_progress');
            $table->foreignId('module_quiz_attempt_id')->nullable()->constrained('module_quiz_attempts')->nullOnDelete();
            $table->dateTime('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'course_module_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_quiz_sessions');
    }
};
