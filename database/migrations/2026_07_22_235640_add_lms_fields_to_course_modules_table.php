<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_modules', function (Blueprint $table) {
            $table->unsignedTinyInteger('passing_score')->default(90)->after('quiz_time_limit_minutes');
            $table->unsignedTinyInteger('max_attempts')->default(1)->after('passing_score');
            $table->json('materials')->nullable()->after('max_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('course_modules', function (Blueprint $table) {
            $table->dropColumn(['passing_score', 'max_attempts', 'materials']);
        });
    }
};
