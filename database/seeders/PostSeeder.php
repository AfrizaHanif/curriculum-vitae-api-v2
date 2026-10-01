<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    use SeedsLocalFiles;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $posts = [
            [
                'id' => 'BLG-001',
                'profile_id' => $user->profile->id,
                'title' => 'Selamat Datang di Web Portfolio',
                'slug' => 'selamat-datang-di-web-portfolio',
                'category' => 'Announcement',
                'author' => 'Afriza Hanif',
                'tags' => ['Announcement'],
                'summary' => 'Ini adalah postingan pertama dari saya.',
                'image' => '/images/posts/BLG-001.png',
                'content' => '<h2>Selamat Datang di Web Portfolio!</h2><p>Ini adalah postingan pertama yang saya buat untuk mendemonstrasi apakah kontent blog sudah berfungsi atau tidak. Selamat datang di web portfolio saya!</p>',
                'is_featured' => true,
            ],
            [
                'id' => 'BLG-002',
                'profile_id' => $user->profile->id,
                'title' => 'Hasil Proyek Kerja Praktik dan Tugas Akhir',
                'slug' => 'hasil-proyek-kerja-praktik-dan-tugas-akhir',
                'category' => 'Announcement',
                'author' => 'Afriza Hanif',
                'tags' => ['Announcement'],
                'summary' => 'Proyek-proyek selama saya berkuliah telah dapat anda lihat di halaman Portfolio.',
                'image' => '/images/posts/BLG-002.png',
                'content' => '<p>Sekarang anda dapat melihat proyek-proyek yang telah saya kerjakan selaman perkuliahan berlangsung. Anda dapat melihat proyey-proyek tersebut di kategori proyek akademik.</p>',
            ],
            [
                'id' => 'BLG-003',
                'profile_id' => $user->profile->id,
                'title' => 'Update Portfolio CV ke React',
                'slug' => 'update-portfolio-cv-ke-react',
                'category' => 'Announcement',
                'author' => 'Afriza Hanif',
                'tags' => ['Announcement'],
                'summary' => 'Portfolio ini telah menggunakan React, menggantikan Angular.',
                'image' => '/images/posts/BLG-003.png',
                'content' => '<p>Akhirnya, saya berhasil melakukan migrasi front-end framework dari Angular ke React setelah melakukan percobaan pembuatan web portfolio terbaru menggunakan React</p><p>Akan tetapi, masih ada beberapa hal yang perlu saya pelajari mengenai React dan cara mengurangi duplikasi kode (DRY Method)</p>',
            ],
        ];

        foreach ($posts as $post) {
            Post::updateOrCreate(['id' => $post['id']], $post);
            $this->seedFile($post['image'] ?? null);
        }
    }
}
