<?php

namespace Database\Seeders;

use App\Models\CaseStudy;
use Illuminate\Database\Seeder;

class CaseStudySeeder extends Seeder
{
    use SeedsLocalFiles;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $caseStudies = [
            [
                'id' => 'CAS-001',
                'portfolio_id' => 'POR-001',
                'role' => 'Full-Stack Developer',
                'responsibilities' => [
                    'Mengerjakan proyek sesuai dengan kebutuhan dari pegawai PST',
                    'Mengerjakan proyek tanpa mengubah data asli yang ada di BPS Jawa Timur',
                ],
                'problems' => [
                    'User Experience (UX) yang Kurang Optimal: Tata letak antarmuka (interface) belum mengikuti standar usability, sehingga menyulitkan navigasi pengguna dalam mengoperasikan aplikasi',
                    'Aksesibilitas Terbatas: Operasional aplikasi masih bersifat tertutup (eksklusif) hanya untuk Pegawai PST, sehingga menghambat koordinasi data antar departemen/unit lain',
                    'Keterbatasan Fungsionalitas: Fitur-fitur yang tersedia belum mencakup seluruh proses bisnis yang dibutuhkan, sehingga masih diperlukan proses manual di luar sistem',
                    'Ketiadaan Modul Pelaporan: Sistem belum memiliki fitur manajemen laporan, yang mengakibatkan sulitnya proses pengambilan keputusan berbasis data bagi pihak manajemen',
                ],
                'goals' => ['Merancang Bangun Aplikasi Pelayanan Perpustakaan Pada Badan Pusat Statistik Provinsi Jawa Timur'],
                'diagrams' => [
                    [
                        'name' => 'Context Diagram',
                        'images' => '/images/portfolios/case-study/CAS-001-Context.jpg',
                    ],
                    [
                        'name' => 'DFD Level 0',
                        'images' => '/images/portfolios/case-study/CAS-001-DFD0.jpg',
                    ],
                    [
                        'name' => 'PDM',
                        'images' => '/images/portfolios/case-study/CAS-001-PDM.jpg',
                    ],
                ],
                'benefits' => [
                    'Mempermudah pengguna untuk mengakses daftar buku dan daftar isi dengan cepat',
                    'Mempermudah dan mempercepat pengguna untuk melakukan pengisian peminjaman dan pengembalian buku',
                    'Mempermudah pegawai / pengunjung untuk mencari informasi terkait dengan buku yang dicari',
                ],
                'solutions' => [
                    [
                        'title' => 'Penggunaan Bootstrap sebagai alat desain Frontend',
                        'context' => 'Saya telah melakukan desain ulang pada aplikasi ini agar tata letak antarmuka (Interface) terlihat lebih rapi menggunakan Bootstrap yang dapat memudahkan saya untuk merapikan elemen-elemen yang ada di halaman-halaman pada aplikasi tersebut',
                    ],
                    [
                        'title' => 'Aksesibilitas untuk non-pegawai PST',
                        'context' => 'Agar para pegawai dapat mengakses daftar buku apa saja yang ada di perpustakaan, saya telah membangun ulang halaman utama pada aplikasi agar pengguna dapat mengakses daftar buku yang ada',
                    ],
                ],
                'process' => [
                    'Setelah dilakukannya observasi dan wawancara, saya melakukan pengumpulan data mengenai masalah-masalah yang ada dan kebutuhan data untuk pembuatan aplikasi pada proyek ini',
                    'Agar proyek ini berjalan dengan baik, saya menggunakan metode pengembangan aplikasi Waterfall milik Pressman tahun 2015',
                    'Sebelum dilakukannya pembuatan aplikasi, saya membuat sebuah diagram (Context Diagram, DFD Level 0, dan PDM) yang memudahkan saya untuk merencanakan pembangunan aplikasi pada proyek ini',
                    'Saya memilih Framework Laravel karena fitur Eloquent ORM yang memudahkan pengelolaan database relasional yang kompleks serta sistem keamanan built-in yang kuat untuk melindungi data instansi',
                    'Proses pengembangan dilakukan secara bertahap, dimulai dari pembangunan skema database hingga perancangan kode pada sisi Frontend dan Backend',
                ],
                'challenges' => [
                    'Saat mengerjakan proyek ini, saya membutuhkan waktu sedikit lama untuk mempelajari CRUD (Memasukkan, mengubah, membaca, dan menghapus data), Laravel, dan Bootstrap',
                    'Dalam waktu yang sangat singkat (Batasan waktu KP), tampilan dari aplikasi yang telah saya buat masih standar',
                ],
                'lessons' => [
                    'Dari proyek ini, saya akan mempelajari lebih dalam mengenai Laravel dan Bootstrap',
                    'Saya akan meningkatkan tampilan halaman yang lebih menarik',
                    'Saya akan melakukan ekspesimen CRUD hanya dalam satu halaman',
                ],
                'results' => [
                    'Pegawai PST dapat mencatat dan menampilkan daftar buku, daftar isi, pegawai, dan transaksi peminjaman dan pengembalian',
                    'Dengan adanya aplikasi yang telah saya buat, pegawai PST akan terbantu dalam mencari buku dan daftar isi yang dibutuhkan oleh pegawai lain tanpa harus berpindah dari halaman ke halaman lain',
                    'Pegawai PST dapat melakukan import / memasukkan data-data buku dan daftar isi dari file Excel agar pegawai PST tidak perlu melakukan input manual ketika server tersebut bermasalah atau pindah server',
                ],
            ],
            [
                'id' => 'CAS-002',
                'portfolio_id' => 'POR-002',
                'role' => 'Full-Stack Developer',
                'responsibilities' => [
                    'Mengerjakan proyek sesuai dengan kebutuhan dari pengguna (Kepegawaian, Kepala BPS Jawa Timur, dan seluruh pegawai BPS Jawa Timur)',
                    'Mengerjakan proyek tanpa mengubah data nilai asli yang dimiliki oleh BPS Jawa Timur',
                ],
                'problems' => [
                    'Presensi saat melakukan apel Senin dan senam Jumat masih menggunakan presensi manual sehingga proses rekap akan berlangsung lama karena diselingi aktivitas lain dan perlu dilakukan proses entry sampai satu minggu',
                    'Belum ada pembobotan untuk menilai secara kualitatif kinerja karyawan yang didasarkan pada indikator BerAkhlak',
                ],
                'goals' => [
                    'Menghasilkan aplikasi penentuan karyawan terbaik pada BPS Jawa Timur',
                    'Penilaian kinerja dapat ditentukan berdasarkan kinerja karyawan yang sebenarnya',
                    'Dapat memenuhi kriteria kedisiplinan (Kehadiran dan Keterlambatan), kinerja karyawan (CKP) dan 7 perilaku BerAkhlak (Berorientasi Pelayanan, Akuntabel, Kompeten, Harmonis, Loyal, Adaptif, dan Kolaboratif) serta memiliki integritas yang tinggi secara objektif',
                ],
                'diagrams' => [
                    [
                        'name' => 'Context Diagram',
                        'images' => '/images/portfolios/case-study/CAS-002-Context.png',
                    ],
                    [
                        'name' => 'DFD Level 0',
                        'images' => '/images/portfolios/case-study/CAS-002-DFD0.png',
                    ],
                    [
                        'name' => 'PDM',
                        'images' => '/images/portfolios/case-study/CAS-002-PDM.png',
                    ],
                ],
                'benefits' => [
                    'Mempermudah dan mempercepat pengguna untuk melakukan pengisian nilai kinerja untuk melakukan proses karyawan terbaik',
                    'Mempermudah pengguna untuk mengakses daftar nilai di setiap kriteria',
                ],
                'solutions' => [
                    [
                        'title' => 'Menggantikan cara tradisional menjadi aplikasi web',
                        'context' => 'Saya membuat aplikasi web untuk menggantikan cara tradisional dari masalah yang ada agar memudahkan pengguna untuk mengisi data dengan cepat menggunakan fitur import file ke aplikasi web',
                    ],
                    [
                        'title' => 'Menggunakan Simple Additive Weighting (SAW) sebagai metode pemilihan keputusan untuk perhitungan karyawan terbaik',
                        'context' => 'Untuk mementukan pemilihan karyawan terbaik menggunakan bobot, saya menggunakan Simple Additive Weighting (SAW) sebagai metode pemilihan keputusan',
                    ],
                ],
                'process' => [
                    'Setelah dilakukannya observasi dan wawancara, saya melakukan pengumpulan data mengenai masalah-masalah yang ada dan kebutuhan data untuk pembuatan aplikasi pada proyek ini',
                    'Agar proyek ini berjalan dengan baik, saya menggunakan model pengembangan aplikasi Waterfall milik Sommerville tahun 2016',
                    'Sebelum dilakukannya pembuatan aplikasi, saya membuat sebuah diagram (Context Diagram, DFD Level 0, dan PDM) yang memudahkan saya untuk merencanakan pembangunan aplikasi pada proyek ini',
                    'Saya memilih Framework Laravel karena fitur Eloquent ORM yang memudahkan pengelolaan database relasional yang kompleks serta sistem keamanan built-in yang kuat untuk melindungi data instansi',
                    'Proses pengembangan dilakukan secara bertahap, dimulai dari pembangunan skema database hingga perancangan kode pada sisi Frontend dan Backend',
                ],
                'challenges' => [
                    'Pada proyek ini membutuhkan waktu lama untuk mempelajari metode SAW untuk digunakan pada aplikasi ini',
                ],
                'lessons' => [
                    'Dari proyek yang saya kerjakan, untuk kedepannya saya akan mempelajari cara mempersingkat kodingan agar terlihat lebih ringkas',
                ],
                'results' => [
                    'Kepegawaian dapat mengelola data karyawan, pengguna, kriteria, dan periode dalam satu halaman saja',
                    'Dengan adanya aplikasi yang telah saya buat, pengguna dapat melakukan pemasukan data karyawan dan data nilai menggunakan import untuk mempercepat proses saat melakukan pemasukan kedua data tersebut',
                    'Proyek ini dapat melakukan analisis SAW dan menghasilkan laporan (Lap. Karyawan, Lap. Hasil Analisis SAW, Lap. Nilai Akhir, Lap. Nilai Pegawai, Lap. Karyawan Terbaik, Sertifikat Karyawan Terbaik)',
                    'Total waktu responsif pada aplikasi yang telah saya buat sekitar 01.79 Menit dibandingkan dengan waktu sebelum adanya aplikasi tersebut sekitar 4 sampai 5 minggu',
                ],
            ],
        ];

        foreach ($caseStudies as $caseStudy) {
            CaseStudy::updateOrCreate(['id' => $caseStudy['id']], $caseStudy);

            foreach ($caseStudy['diagrams'] ?? [] as $diagram) {
                if (! empty($diagram['images'])) {
                    $this->seedFile($diagram['images']);
                }
            }
        }
    }
}
