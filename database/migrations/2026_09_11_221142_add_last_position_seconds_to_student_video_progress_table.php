<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_video_progress', function (Blueprint $table) {
            $table->unsignedInteger('last_position_seconds')->default(0)->after('duration_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('student_video_progress', function (Blueprint $table) {
            $table->dropColumn('last_position_seconds');
        });
    }
};
