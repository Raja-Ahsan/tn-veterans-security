<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $legacy = [
            [
                'name' => 'Handgun Carry Permit',
                'slug' => 'homeland_security',
                'menu_group' => 'training',
                'link_type' => 'category',
                'link_value' => 'homeland_security',
                'sort_order' => 10,
                'show_in_nav' => false,
                'assignable' => true,
            ],
            [
                'name' => 'Handgun Carry Permit (alt)',
                'slug' => 'handgun_carry',
                'menu_group' => 'training',
                'link_type' => 'category',
                'link_value' => 'handgun_carry',
                'sort_order' => 11,
                'show_in_nav' => false,
                'assignable' => false,
            ],
            [
                'name' => 'ASP Less than Lethal',
                'slug' => 'asp_less_than_lethal',
                'menu_group' => 'training',
                'link_type' => 'category',
                'link_value' => 'asp_less_than_lethal',
                'sort_order' => 12,
                'show_in_nav' => false,
                'assignable' => true,
            ],
            [
                'name' => 'Dallas Law',
                'slug' => 'dallas_law',
                'menu_group' => 'security',
                'link_type' => 'category',
                'link_value' => 'dallas_law',
                'sort_order' => 10,
                'show_in_nav' => false,
                'assignable' => true,
            ],
            [
                'name' => 'Security Guard',
                'slug' => 'security_guard',
                'menu_group' => 'training',
                'link_type' => 'category',
                'link_value' => 'security_guard',
                'sort_order' => 13,
                'show_in_nav' => false,
                'assignable' => true,
            ],
            [
                'name' => 'Security Training',
                'slug' => 'security_training',
                'menu_group' => 'security',
                'link_type' => 'category',
                'link_value' => 'security_training',
                'sort_order' => 11,
                'show_in_nav' => false,
                'assignable' => true,
            ],
        ];

        foreach ($legacy as $row) {
            $exists = DB::table('service_categories')->where('slug', $row['slug'])->exists();
            if ($exists) {
                continue;
            }

            DB::table('service_categories')->insert([
                ...$row,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Prefer a clean name if homeland_security already exists with a bad label.
        DB::table('service_categories')
            ->where('slug', 'homeland_security')
            ->update([
                'name' => 'Handgun Carry Permit',
                'menu_group' => 'training',
                'assignable' => true,
                'updated_at' => $now,
            ]);

        DB::table('service_categories')
            ->where('slug', 'handgun_carry')
            ->update([
                'assignable' => false,
                'updated_at' => $now,
            ]);
    }

    public function down(): void
    {
        DB::table('service_categories')->whereIn('slug', [
            'homeland_security',
            'handgun_carry',
            'asp_less_than_lethal',
            'dallas_law',
            'security_guard',
            'security_training',
        ])->delete();
    }
};
