<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('location')->constrained()->nullOnDelete();
            $table->foreignId('instructor_id')->nullable()->after('instructor')->constrained()->nullOnDelete();
            $table->boolean('admin_override_capacity')->default(false)->after('status');
            $table->text('travel_notes')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropForeign(['instructor_id']);
            $table->dropColumn(['location_id', 'instructor_id', 'admin_override_capacity', 'travel_notes']);
        });
    }
};
