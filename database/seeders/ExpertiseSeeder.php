<?php

namespace Database\Seeders;

use App\Models\Expertise;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpertiseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $expertises = [
            [
                'id' => 'XPT-001',
                'profile_id' => $user->profile->id,
                'title' => 'Full-Stack Developer Laravel',
                'description' => [
                    'id' => 'Saya adalah Full-Stack Developer dengan spesialisasi pada framework Laravel, berpengalaman dalam membangun aplikasi web yang scalable, aman, dan user-friendly. Memiliki pemahaman mendalam terhadap arsitektur MVC, RESTful API, dan pengembangan sisi frontend maupun backend.',
                    'en' => 'I am a Full-Stack Developer specializing in the Laravel framework, experienced in building scalable, secure, and user-friendly web applications. I possess a deep understanding of MVC architecture, RESTful APIs, and both frontend and backend development.',
                ],
                'icon' => 'code-slash',
                'portfolios' => ['POR-001', 'POR-002'],
            ],
        ];

        foreach ($expertises as $data) {
            $portfolioIds = $data['portfolios'] ?? [];
            unset($data['portfolios']);

            $expertise = Expertise::updateOrCreate(['id' => $data['id']], $data);
            if (! empty($portfolioIds)) {
                $expertise->portfolios()->sync($portfolioIds);
            }
        }
    }
}
