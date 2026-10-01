<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    use SeedsLocalFiles;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrFail();

        Profile::updateOrCreate(['id' => 'PRO-001'], [
            'id' => 'PRO-001',
            'user_id' => $user->id,
            'fullname' => 'Muhammad Afriza Hanif',
            'phone' => env('SEEDER_PROFILE_PHONE', '+6281234567890'),
            'current_city' => 'Sidoarjo',
            'current_province' => 'Jawa Timur',
            'email' => env('SEEDER_PROFILE_EMAIL', 'contact@example.com'),
            'birthday' => env('SEEDER_PROFILE_BIRTHDAY', '1996-01-01'),
            'tagline' => [
                'id' => 'Junior Web Developer | Full-Stack Enthusiast | Berdedikasi Membangun Solusi Digital Berdampak',
                'en' => 'Junior Web Developer | Full-Stack Enthusiast | Dedicated to Building Impactful Digital Solutions',
            ],
            'description' => [
                'id' => 'Lulusan Sistem Informasi dengan pengalaman praktis di industri dalam pengembangan web full‑stack menggunakan Laravel, React, PHP, dan MySQL. Telah menyelesaikan magang di BPS Jawa Timur (pengembangan backend, manajemen data, dan optimasi sistem) serta Kominfo Jawa Timur (pengembangan frontend, perbaikan konsistensi UI/UX, dan stabilitas performa). Terampil dalam mengimplementasikan algoritma seperti Simple Additive Weighting (SAW) untuk sistem pendukung keputusan. Berkomitmen menulis kode yang bersih, terstruktur, dan siap berkontribusi sebagai Web Developer pada proyek yang berdampak nyata.',
                'en' => 'Information Systems graduate with practical industry experience in full-stack web development using Laravel, React, PHP, and MySQL. Completed internships at BPS Jawa Timur (backend development, data management, and system optimization) and Kominfo Jawa Timur (frontend development, UI/UX consistency, and system stability). Skilled in algorithm implementation such as Simple Additive Weighting (SAW) for automated decision support systems. Dedicated to writing clean, maintainable code and eager to contribute as a Web Developer to high-impact projects.',
            ],
            'philosophy' => [
                'id' => 'Sebagai web programmer, saya meyakini bahwa teknologi harus memberikan solusi yang efisien, aman, dan berkelanjutan. Saya berkomitmen untuk menulis kode yang terstruktur dan mudah dipelihara, mengikuti standar industri, serta beradaptasi dengan perkembangan teknologi dan kebutuhan bisnis. Dalam setiap proyek, saya menjunjung tinggi profesionalisme, komunikasi yang jelas, dan integritas kerja untuk menghasilkan sistem yang fungsional dan berdampak.',
                'en' => 'As a web developer, I believe technology should provide efficient, secure, and sustainable solutions. I am committed to writing clean, maintainable code that adheres to industry standards while embracing modern web innovations. In every project, I value professionalism, clear communication, and integrity to deliver meaningful digital experiences.',
            ],
            'status' => 'Junior Web Developer',
            'formal_photo' => '/images/profile_formal.jpg',
            'setup_image' => '/images/profile_setup.png',
            'resume' => '/pdfs/CV_Muhammad_Afriza_Hanif.pdf',
        ]);

        $this->seedFile('/images/profile_formal.jpg');
        $this->seedFile('/images/profile_setup.png');
        $this->seedFile('/pdfs/CV_Muhammad_Afriza_Hanif.pdf');
    }
}
