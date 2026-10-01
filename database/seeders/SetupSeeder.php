<?php

namespace Database\Seeders;

use App\Models\Setup;
use App\Models\User;
use Illuminate\Database\Seeder;

class SetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $setups = [
            [
                'id' => 'SET-001',
                'profile_id' => $user->profile->id,
                'name' => 'Komputer Utama',
                'category' => 'Home Workstation',
                'description' => 'PC Rakitan (AMD Ryzen 5, 16GB RAM)',
                'reason' => ['Komputer ini digunakan untuk melakukan pemrograman dengan cepat dan stabil tanpa bottleneck'],
            ],
            [
                'id' => 'SET-002',
                'profile_id' => $user->profile->id,
                'name' => 'Monitor',
                'category' => 'Home Workstation',
                'description' => 'LG 19M38A 19 inch',
                'reason' => ['Menghasilkan layar yang cukup luas untuk melakukan multitasking saat melakukan pengerjaan'],
            ],
            [
                'id' => 'SET-003',
                'profile_id' => $user->profile->id,
                'name' => 'Keyboard',
                'category' => 'Home Workstation',
                'description' => 'Logitech Pebble Keys 2 K380s',
                'reason' => ['Ringkas dan nyaman. Layout keyboard ini beradaptasi dengan keyboard laptop 14 inch'],
            ],
            [
                'id' => 'SET-004',
                'profile_id' => $user->profile->id,
                'name' => 'Mouse',
                'category' => 'Home Workstation',
                'description' => 'Logitech Pebble M350',
                'reason' => ['Ringkas dan hening tanpa mengganggu aktivitas'],
            ],
            [
                'id' => 'SET-005',
                'profile_id' => $user->profile->id,
                'name' => 'Laptop',
                'category' => 'Portable Setup',
                'description' => 'Lenovo Ideapad 3 (2021)',
                'reason' => ['Laptop ini digunakan untuk melakukan programming dimana saja saat bepergian'],
            ],
            [
                'id' => 'SET-006',
                'profile_id' => $user->profile->id,
                'name' => 'Mouse',
                'category' => 'Portable Setup',
                'description' => 'Logitech M240',
                'reason' => ['Ergonomic dan hening tanpa mengganggu aktivitas orang lain.'],
            ],
            [
                'id' => 'SET-007',
                'profile_id' => $user->profile->id,
                'name' => 'Primary Phone',
                'category' => 'Gadget Devices',
                'description' => 'Samsung Galaxy A54 5G',
            ],
            [
                'id' => 'SET-008',
                'profile_id' => $user->profile->id,
                'name' => 'Wearable',
                'category' => 'Gadget Devices',
                'description' => 'Samsung Galaxy Fit3',
            ],
            [
                'id' => 'SET-009',
                'profile_id' => $user->profile->id,
                'name' => 'Code Editor',
                'category' => 'Shared Development Tools',
                'description' => 'Visual Studio Code',
                'reason' => ['Cepat, dapat disesuaikan dengan ekosistem ekstensi yang luas, dan memiliki dukungan bawaan yang hebat untuk banyak bahasa pemrograman yang saya gunakan'],
            ],
            [
                'id' => 'SET-010',
                'profile_id' => $user->profile->id,
                'name' => 'Terminal',
                'category' => 'Shared Development Tools',
                'description' => 'Windows Terminal with PowerShell',
                'reason' => ['Terminal modern yang mendukung banyak tab, panel, dan profil. PowerShell sangat kuat untuk scripting dan otomatisasi di Windows'],
            ],
            [
                'id' => 'SET-011',
                'profile_id' => $user->profile->id,
                'name' => 'Browser',
                'category' => 'Shared Development Tools',
                'description' => 'Google Chrome & Firefox Developer Edition',
                'reason' => ['Saya menggunakan Google Chrome karena alat pengembangnya yang tangguh dan kompatibilitasnya yang luas, dan Firefox Developer Edition karena inspector CSS grid-nya yang luar biasa dan fitur privasinya'],
            ],
            [
                'id' => 'SET-012',
                'profile_id' => $user->profile->id,
                'name' => 'API Client',
                'category' => 'Shared Development Tools',
                'description' => 'Postman',
                'reason' => ['Sebuah alat yang tak tergantikan untuk menguji dan berinteraksi dengan API. Alat ini dapat menyederhanakan proses pengiriman permintaan dan pemeriksaan respons'],
            ],
        ];

        foreach ($setups as $setup) {
            Setup::updateOrCreate(['id' => $setup['id']], $setup);
        }
    }
}
