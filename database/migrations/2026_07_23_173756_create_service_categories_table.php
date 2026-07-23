<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('menu_group')->default('training'); // training | security
            $table->string('link_type')->default('category'); // category | slug | route
            $table->string('link_value');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('show_in_nav')->default(true);
            $table->boolean('assignable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        $rows = [
            // Training & Classes (public dropdown)
            ['name' => 'NRA', 'slug' => 'nra', 'menu_group' => 'training', 'link_type' => 'category', 'link_value' => 'nra', 'sort_order' => 1],
            ['name' => 'Red Cross', 'slug' => 'red_cross', 'menu_group' => 'training', 'link_type' => 'category', 'link_value' => 'red_cross', 'sort_order' => 2],
            ['name' => 'Enhanced Handgun Carry Permit', 'slug' => 'enhanced_handgun_carry', 'menu_group' => 'training', 'link_type' => 'slug', 'link_value' => 'enhanced-handgun-carry-permit', 'sort_order' => 3],
            ['name' => 'Active Shooter 8 Hours', 'slug' => 'active_shooter', 'menu_group' => 'training', 'link_type' => 'slug', 'link_value' => 'active-shooter', 'sort_order' => 4],
            ['name' => 'Force Science (De-Escalation)', 'slug' => 'force_science', 'menu_group' => 'training', 'link_type' => 'slug', 'link_value' => 'forced-science-de-escalation', 'sort_order' => 5],
            ['name' => 'Handle With Care', 'slug' => 'handle_with_care', 'menu_group' => 'training', 'link_type' => 'slug', 'link_value' => 'handle-with-care', 'sort_order' => 6],
            // Security Training dropdown
            ['name' => 'Initial Registration', 'slug' => 'initial_registration', 'menu_group' => 'security', 'link_type' => 'route', 'link_value' => 'intial-security', 'sort_order' => 1],
            ['name' => 'Renewal Registration', 'slug' => 'renewals', 'menu_group' => 'security', 'link_type' => 'route', 'link_value' => 'renewals', 'sort_order' => 2],
        ];

        foreach ($rows as $row) {
            DB::table('service_categories')->insert([
                ...$row,
                'show_in_nav' => true,
                'assignable' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('service_categories');
    }
};
