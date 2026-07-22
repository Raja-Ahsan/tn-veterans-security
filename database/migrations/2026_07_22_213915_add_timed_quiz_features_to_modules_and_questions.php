<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_modules', function (Blueprint $table) {
            $table->unsignedInteger('quiz_time_limit_minutes')->nullable()->after('is_active');
        });

        Schema::table('module_quiz_questions', function (Blueprint $table) {
            $table->boolean('allow_multiple')->default(false)->after('options');
        });

        // Convert single-string correct answers into JSON arrays for multi-select support.
        $questions = DB::table('module_quiz_questions')->select('id', 'correct_answer')->get();
        foreach ($questions as $question) {
            $raw = $question->correct_answer;
            if ($raw === null || $raw === '') {
                continue;
            }

            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                continue;
            }

            DB::table('module_quiz_questions')
                ->where('id', $question->id)
                ->update(['correct_answer' => json_encode([(string) $raw])]);
        }
    }

    public function down(): void
    {
        Schema::table('module_quiz_questions', function (Blueprint $table) {
            $table->dropColumn('allow_multiple');
        });

        Schema::table('course_modules', function (Blueprint $table) {
            $table->dropColumn('quiz_time_limit_minutes');
        });
    }
};
