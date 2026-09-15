<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Online and Blended are the same offering type: classes with online modules.
        DB::table('services')
            ->where('has_online_parts', true)
            ->where('testing_in_person', false)
            ->update(['testing_in_person' => true]);
    }

    public function down(): void
    {
        // Irreversible data normalization.
    }
};
