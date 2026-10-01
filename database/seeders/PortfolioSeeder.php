<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    use SeedsLocalFiles;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $portfolios = [
            [
                'id' => 'POR-001',
                'profile_id' => $user->profile->id,
                'title' => 'Rancang Bangun Aplikasi Pelayanan Perpustakaan pada Badan Pusat Statistik Provinsi Jawa Timur',
                'slug' => 'rancang-bangun-aplikasi-pelayanan-perpustakaan-pada-badan-pusat-statistik-provinsi-jawa-timur',
                'type' => 'Proyek Akademik',
                'category' => 'Full-Stack Development',
                'image' => '/images/portfolios/POR-001.png',
                'gallery' => [
                    '/images/portfolios/gallery/IMG-001-001.png',
                    '/images/portfolios/gallery/IMG-001-002.png',
                    '/images/portfolios/gallery/IMG-001-003.png',
                    '/images/portfolios/gallery/IMG-001-004.png',
                    '/images/portfolios/gallery/IMG-001-005.png',
                    '/images/portfolios/gallery/IMG-001-006.png',
                    '/images/portfolios/gallery/IMG-001-007.png',
                    '/images/portfolios/gallery/IMG-001-008.png',
                    '/images/portfolios/gallery/IMG-001-009.png',
                    '/images/portfolios/gallery/IMG-001-010.png',
                    '/images/portfolios/gallery/IMG-001-011.png',
                    '/images/portfolios/gallery/IMG-001-012.png',
                ],
                'start_period' => '2023-01-01',
                'finish_period' => '2023-07-02',
                'description' => [
                    'id' => [
                        'Mengembangkan sistem perpustakaan berbasis web menggunakan Laravel, PHP, MySQL, HTML, dan Bootstrap.',
                        'Mengimplementasikan fungsionalitas CRUD untuk mengelola data buku, daftar isi, data staf, serta transaksi peminjaman/pengembalian.',
                        'Merancang fitur pencarian agar staf dapat menemukan buku dan isi dengan cepat tanpa harus membuka banyak halaman.',
                        'Membangun kemampuan import Excel untuk mempercepat entri data massal dan mengurangi pemasukan data secara manual saat migrasi server atau downtime.',
                        'Menambahkan fitur ekspor ke Excel/PDF, memungkinkan staf menghasilkan laporan dan mencetak data dengan lebih efisien.',
                    ],
                    'en' => [
                        'Developed a web-based library system using Laravel, PHP, MySQL, HTML, and Bootstrap.',
                        'Implemented CRUD functionality to manage books, table of contents, staff records, and loan/return transactions.',
                        'Designed features to help staff quickly search books and contents without navigating multiple pages.',
                        'Built Excel import capability to streamline bulk data entry and reduce manual input during server migration or downtime.',
                        'Added export to Excel/PDF functionality, enabling staff to generate reports and print records efficiently.',
                    ],
                ],
                'tags' => ['Kerja Praktik', 'Aplikasi Web', 'BPS', 'Perpustakaan'],
                'technology' => ['Laravel', 'PHP', 'Bootstrap'],
                'repositories' => [
                    [
                        'name' => 'Dokumen',
                        'url' => 'https://repository.dinamika.ac.id/id/eprint/7167/',
                        'icon' => 'file-earmark-text',
                    ],
                ],
                'features' => [
                    [
                        'id' => 'FEA-001',
                        'title' => 'Daftar buku dan daftar isi untuk pengunjung',
                        'description' => 'Pengunjung dapat melihat daftar buku dan daftar isi yang telah dirapikan.',
                        'progress' => 100,
                    ],
                    [
                        'id' => 'FEA-002',
                        'title' => 'Export detail buku ke PDF',
                        'description' => 'Pengguna dapat export detail buku ke file PDF yang dapat disimpan oleh pengguna.',
                        'progress' => 100,
                    ],
                    [
                        'id' => 'FEA-003',
                        'title' => 'Import & Export Excel',
                        'description' => 'Pegawai PST dapat melakukan import buku dan daftar isi dari Excel untuk memudahkan penambahan data dan export ke Excel untuk memudahkan pengambilan data.',
                        'progress' => 100,
                    ],
                ],
            ],
            [
                'id' => 'POR-002',
                'profile_id' => $user->profile->id,
                'title' => 'Rancang Bangun Aplikasi Penentuan Karyawan Terbaik Berbasis Web menggunakan Metode Simple Additive Weighting (SAW) pada BPS Provinsi Jawa Timur',
                'slug' => 'rancang-bangun-aplikasi-penentuan-karyawan-terbaik-berbasis-web-menggunakan-metode-simple-additive-weighting-saw-pada-bps-provinsi-jawa-timur',
                'type' => 'Proyek Akademik',
                'category' => 'Full-Stack Development',
                'image' => '/images/portfolios/POR-002.png',
                'gallery' => [
                    '/images/portfolios/gallery/IMG-002-001.png',
                    '/images/portfolios/gallery/IMG-002-002.png',
                    '/images/portfolios/gallery/IMG-002-003.png',
                    '/images/portfolios/gallery/IMG-002-004.png',
                    '/images/portfolios/gallery/IMG-002-005.png',
                    '/images/portfolios/gallery/IMG-002-006.png',
                    '/images/portfolios/gallery/IMG-002-007.png',
                    '/images/portfolios/gallery/IMG-002-008.png',
                    '/images/portfolios/gallery/IMG-002-009.png',
                    '/images/portfolios/gallery/IMG-002-010.png',
                    '/images/portfolios/gallery/IMG-002-011.png',
                    '/images/portfolios/gallery/IMG-002-012.png',
                ],
                'video' => 'https://www.youtube.com/watch?v=sHs9qlqrsZc',
                'start_period' => '2024-10-01',
                'finish_period' => '2025-01-01',
                'description' => [
                    'id' => [
                        'Merancang dan mengembangkan solusi full-stack menggunakan Laravel, PHP, MySQL, HTML, dan Bootstrap.',
                        'Mengimplementasikan algoritma Simple Additive Weighting (SAW) untuk mengotomatisasi evaluasi kriteria, sehingga meningkatkan efisiensi pengambilan keputusan.',
                        'Membangun sistem autentikasi pengguna yang aman serta kontrol akses berbasis peran untuk melindungi data evaluasi yang sensitif.',
                        'Mengoptimalkan proses pemilihan karyawan terbaik menjadi 1,79 menit untuk 10 kriteria, memangkas waktu dari proses manual sebelumnya yang membutuhkan 4–5 minggu.',
                    ],
                    'en' => [
                        'Designed and developed a full-stack solution using Laravel, PHP, MySQL, HTML, and Bootstrap.',
                        'Implemented the Simple Additive Weighting (SAW) algorithm to automate criteria evaluation, improving decision-making efficiency.',
                        'Built secure user authentication and role-based access control to protect sensitive evaluation data.',
                        'Optimized the employee selection process to 1.79 minutes for 10 criteria, reducing the manual timeline of 4–5 weeks.',
                    ],
                ],
                'tags' => [
                    'Tugas Akhir',
                    'Aplikasi Web',
                    'BPS',
                    'Simple Additive Weighting',
                    'Sistem Pendukung Keputusan',
                ],
                'technology' => ['Laravel', 'PHP', 'Bootstrap'],
                'repositories' => [
                    [
                        'name' => 'Dokumen',
                        'url' => 'https://repository.dinamika.ac.id/id/eprint/7908/',
                        'icon' => 'file-earmark-text',
                    ],
                    [
                        'name' => 'Source Code (GitHub)',
                        'url' => 'https://github.com/AfrizaHanif/tugas_akhir_s1/',
                        'icon' => 'github',
                    ],
                    [
                        'name' => 'Jurnal (JATI)',
                        'url' => 'https://ojs.unikom.ac.id/index.php/jati/article/view/15243/',
                        'icon' => 'journal-text',
                    ],
                ],
                'features' => [
                    [
                        'id' => 'FEA-004',
                        'title' => 'Dashboard interaktif',
                        'description' => 'Kepegawaian, dan kepala BPS Jawa Timur dapat melihat status-status data secara interaktif di halaman dashboard.',
                        'progress' => 100,
                    ],
                    [
                        'id' => 'FEA-005',
                        'title' => 'CRUD dalam satu halaman',
                        'description' => 'Kepegawaian dapat melakukan penambahan, penghapusan, perubahan, dan membaca data yang ada hanya dalam satu halaman.',
                        'progress' => 100,
                    ],
                    [
                        'id' => 'FEA-006',
                        'title' => 'Import data dari Excel',
                        'description' => 'Kepegawaian dapat mengimport data Excel ke aplikasi untuk memudahkan pemasukkan nilai dalam pengambilan keputusan.',
                        'progress' => 100,
                    ],
                ],
            ],
        ];

        foreach ($portfolios as $data) {
            // Separate features from the portfolio attributes
            $features = $data['features'] ?? [];
            unset($data['features']);

            // Create or update the Portfolio
            $portfolio = Portfolio::updateOrCreate(['id' => $data['id']], $data);
            $this->seedFile($portfolio->image ?? null);
            $this->seedFiles($portfolio->gallery ?? []);

            // Save each feature through the polymorphic relationship
            foreach ($features as $feature) {
                if (empty($feature['id'])) {
                    $feature['id'] = (new Feature)->generateCustomId();
                }

                $portfolio->features()->updateOrCreate(
                    ['id' => $feature['id']],
                    $feature
                );
            }
        }
    }
}
