<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_module_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_module_id')->constrained('course_modules')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_path')->nullable();
            $table->string('original_name')->nullable();
            $table->unsignedInteger('order')->default(1);
            $table->timestamps();

            $table->index(['course_module_id', 'order']);
        });

        Schema::create('student_video_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('course_module_id')->constrained('course_modules')->cascadeOnDelete();
            $table->foreignId('course_module_video_id')->constrained('course_module_videos')->cascadeOnDelete();
            $table->boolean('video_watched')->default(false);
            $table->timestamp('watched_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->unsignedTinyInteger('best_score')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'course_module_video_id']);
        });

        Schema::table('module_quiz_questions', function (Blueprint $table) {
            $table->foreignId('course_module_video_id')
                ->nullable()
                ->after('course_module_id')
                ->constrained('course_module_videos')
                ->cascadeOnDelete();
        });

        Schema::table('module_quiz_attempts', function (Blueprint $table) {
            $table->foreignId('course_module_video_id')
                ->nullable()
                ->after('course_module_id')
                ->constrained('course_module_videos')
                ->nullOnDelete();
        });

        Schema::table('module_quiz_sessions', function (Blueprint $table) {
            $table->foreignId('course_module_video_id')
                ->nullable()
                ->after('course_module_id')
                ->constrained('course_module_videos')
                ->nullOnDelete();
        });

        $this->backfillLegacyVideos();
    }

    public function down(): void
    {
        Schema::table('module_quiz_sessions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_module_video_id');
        });

        Schema::table('module_quiz_attempts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_module_video_id');
        });

        Schema::table('module_quiz_questions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_module_video_id');
        });

        Schema::dropIfExists('student_video_progress');
        Schema::dropIfExists('course_module_videos');
    }

    private function backfillLegacyVideos(): void
    {
        $modules = DB::table('course_modules')->select('id', 'title', 'video_url')->get();

        foreach ($modules as $module) {
            $hasQuestions = DB::table('module_quiz_questions')
                ->where('course_module_id', $module->id)
                ->exists();

            $videoUrl = trim((string) ($module->video_url ?? ''));

            if ($videoUrl === '' && ! $hasQuestions) {
                continue;
            }

            $videoId = DB::table('course_module_videos')->insertGetId([
                'course_module_id' => $module->id,
                'title' => 'Video 1',
                'video_url' => $videoUrl !== '' ? $videoUrl : null,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('module_quiz_questions')
                ->where('course_module_id', $module->id)
                ->whereNull('course_module_video_id')
                ->update(['course_module_video_id' => $videoId]);
        }
    }
};
