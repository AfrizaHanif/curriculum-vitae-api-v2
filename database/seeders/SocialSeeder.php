<?php

namespace Database\Seeders;

use App\Models\Social;
use App\Models\User;
use Illuminate\Database\Seeder;

class SocialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $socials = [
            [
                'id' => 'SOC-001',
                'profile_id' => $user->profile->id,
                'name' => 'LinkedIn',
                'url' => 'https://linkedin.com/in/afrizahanif',
                'icon' => 'linkedin',
            ],
            [
                'id' => 'SOC-002',
                'profile_id' => $user->profile->id,
                'name' => 'GitHub',
                'url' => 'https://github.com/AfrizaHanif',
                'icon' => 'github',
            ],
        ];

        foreach ($socials as $social) {
            Social::updateOrCreate(['id' => $social['id']], $social);
        }
    }
}
