<?php

namespace Database\Seeders;

use App\Models\Instructor;
use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationsInstructorsSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => "Shooter's Guns, Ammo, and Range", 'address' => '575 Murfreesboro Pike', 'city' => 'Nashville', 'state' => 'TN', 'zip' => '37210', 'order' => 1],
            ['name' => 'Guns and Leather', 'address' => '2216 US-41', 'city' => 'Greenbrier', 'state' => 'TN', 'zip' => '37073', 'order' => 2],
            ['name' => 'Code Blue CPR', 'address' => '640 Spence Lane Suite 125', 'city' => 'Nashville', 'state' => 'TN', 'zip' => '37217', 'order' => 3],
            ['name' => 'TNPTI', 'address' => '1630 S. Church St', 'city' => 'Murfreesboro', 'state' => 'TN', 'zip' => '37130', 'order' => 4],
        ];

        foreach ($locations as $data) {
            Location::firstOrCreate(
                ['name' => $data['name'], 'address' => $data['address']],
                array_merge($data, ['is_active' => true])
            );
        }

        $instructors = [
            ['name' => 'Jayson', 'order' => 1],
            ['name' => 'Kenny', 'order' => 2],
        ];

        foreach ($instructors as $data) {
            Instructor::firstOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['is_active' => true])
            );
        }
    }
}
