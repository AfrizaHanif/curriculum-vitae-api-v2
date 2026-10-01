<?php

namespace Database\Seeders;

use App\Models\Hobby;
use App\Models\User;
use Illuminate\Database\Seeder;

class HobbySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $hobbies = [
            [
                'id' => 'HOB-001',
                'profile_id' => $user->profile->id,
                'name' => 'Fotografi',
                'icon' => 'camera',
            ],
            [
                'id' => 'HOB-002',
                'profile_id' => $user->profile->id,
                'name' => 'Musik',
                'icon' => 'music-note',
            ],
        ];

        foreach ($hobbies as $hobby) {
            Hobby::updateOrCreate(['id' => $hobby['id']], $hobby);
        }
    }
}
