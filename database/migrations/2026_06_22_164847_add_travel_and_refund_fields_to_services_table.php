<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->text('refund_policy')->nullable()->after('deposit_amount');
            $table->text('what_to_bring')->nullable()->after('requirements');
            $table->text('prerequisites')->nullable()->after('what_to_bring');
            $table->boolean('is_travel_based')->default(false)->after('testing_in_person');
            $table->decimal('travel_distance_fee', 10, 2)->nullable()->after('is_travel_based');
            $table->decimal('travel_lodging_fee', 10, 2)->nullable()->after('travel_distance_fee');
            $table->decimal('travel_time_fee', 10, 2)->nullable()->after('travel_lodging_fee');
            $table->unsignedInteger('travel_minimum_students')->nullable()->after('travel_time_fee');
            $table->text('travel_notes')->nullable()->after('travel_minimum_students');
            $table->text('lodging_instructions')->nullable()->after('travel_notes');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'refund_policy',
                'what_to_bring',
                'prerequisites',
                'is_travel_based',
                'travel_distance_fee',
                'travel_lodging_fee',
                'travel_time_fee',
                'travel_minimum_students',
                'travel_notes',
                'lodging_instructions',
            ]);
        });
    }
};
