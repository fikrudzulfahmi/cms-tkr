<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Data awal CMS TKR.
 *
 * Placeholder dalam [KURUNG SIKU] sengaja dibiarkan apa adanya (sesuai
 * spesifikasi desain) sampai data asli sekolah tersedia. Isi lewat
 * Admin > Pengaturan tanpa menyentuh kode.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Administrator',
            'email' => 'admin@tkr.sch.id',
            'email_verified_at' => now(),
            'role' => 'admin',
            'password' => Hash::make('Admin#TKR2026'),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $settings = [
            ['key' => 'site_name', 'value' => 'TKR', 'group' => 'umum', 'type' => 'text', 'label' => 'Nama Situs'],
            ['key' => 'site_tagline', 'value' => 'Kuasai teknologi mobil masa kini', 'group' => 'umum', 'type' => 'text', 'label' => 'Tagline'],
            ['key' => 'site_logo', 'value' => '', 'group' => 'umum', 'type' => 'image', 'label' => 'Logo Situs'],
            ['key' => 'site_favicon', 'value' => '', 'group' => 'umum', 'type' => 'image', 'label' => 'Favicon'],
            ['key' => 'footer_text', 'value' => '© 2026 Jurusan Teknik Kendaraan Ringan — [NAMA SMK]', 'group' => 'umum', 'type' => 'textarea', 'label' => 'Teks Footer'],

            ['key' => 'school_name', 'value' => '[NAMA SMK]', 'group' => 'profil', 'type' => 'text', 'label' => 'Nama Sekolah'],
            ['key' => 'department_name', 'value' => 'Teknik Kendaraan Ringan', 'group' => 'profil', 'type' => 'text', 'label' => 'Nama Jurusan'],
            ['key' => 'address', 'value' => '[Alamat lengkap sekolah]', 'group' => 'profil', 'type' => 'textarea', 'label' => 'Alamat'],
            ['key' => 'phone', 'value' => '[No. telepon / WA]', 'group' => 'profil', 'type' => 'text', 'label' => 'Telepon'],
            ['key' => 'whatsapp', 'value' => '', 'group' => 'kontak', 'type' => 'text', 'label' => 'Nomor WhatsApp admin SPMB (contoh: 081234567890)'],
            ['key' => 'email', 'value' => '[Email sekolah]', 'group' => 'profil', 'type' => 'text', 'label' => 'Email'],
            ['key' => 'maps_embed', 'value' => '', 'group' => 'profil', 'type' => 'textarea', 'label' => 'Google Maps Embed'],
            ['key' => 'facebook_url', 'value' => '', 'group' => 'sosmed', 'type' => 'text', 'label' => 'Facebook URL'],
            ['key' => 'instagram_url', 'value' => '', 'group' => 'sosmed', 'type' => 'text', 'label' => 'Instagram URL'],
            ['key' => 'youtube_url', 'value' => '', 'group' => 'sosmed', 'type' => 'text', 'label' => 'YouTube URL'],
            ['key' => 'tiktok_url', 'value' => '', 'group' => 'sosmed', 'type' => 'text', 'label' => 'TikTok URL'],

            ['key' => 'hero_title', 'value' => 'Teknik Kendaraan Ringan', 'group' => 'beranda', 'type' => 'text', 'label' => 'Hero Judul'],
            ['key' => 'hero_subtitle', 'value' => 'Kuasai teknologi mobil masa kini, dari mesin hingga kendaraan listrik.', 'group' => 'beranda', 'type' => 'textarea', 'label' => 'Hero Subjudul'],
            ['key' => 'hero_bg', 'value' => '', 'group' => 'beranda', 'type' => 'image', 'label' => 'Hero Background'],
            ['key' => 'about_text', 'value' => '<p>Jurusan Teknik Kendaraan Ringan menyiapkan teknisi yang mampu mendiagnosis, merawat, dan memperbaiki mobil secara profesional — dari engine konvensional hingga kendaraan listrik.</p>', 'group' => 'beranda', 'type' => 'html', 'label' => 'Teks Tentang Kami'],
            ['key' => 'about_image', 'value' => '', 'group' => 'beranda', 'type' => 'image', 'label' => 'Foto Tentang Kami'],
            ['key' => 'stat_students', 'value' => '', 'group' => 'beranda', 'type' => 'text', 'label' => 'Statistik: siswa aktif'],
            ['key' => 'stat_graduates', 'value' => '', 'group' => 'beranda', 'type' => 'text', 'label' => 'Statistik: alumni'],
            ['key' => 'stat_absorption', 'value' => '', 'group' => 'beranda', 'type' => 'text', 'label' => 'Statistik: lulusan terserap kerja'],

            ['key' => 'profile_head_name', 'value' => '', 'group' => 'profil', 'type' => 'text', 'label' => 'Nama Kepala Jurusan'],
            ['key' => 'profile_head_position', 'value' => 'Kepala Program Keahlian TKR', 'group' => 'profil', 'type' => 'text', 'label' => 'Jabatan Kepala Jurusan'],
            ['key' => 'profile_head_photo', 'value' => '', 'group' => 'profil', 'type' => 'image', 'label' => 'Foto Kepala Jurusan'],
            ['key' => 'profile_speech', 'value' => 'Assalamu’alaikum warahmatullahi wabarakatuh.

Kami berkomitmen membekali setiap siswa dengan keterampilan nyata, karakter yang kuat, dan kesiapan kerja agar mampu bersaing di dunia industri otomotif.', 'group' => 'profil', 'type' => 'textarea', 'label' => 'Sambutan Kepala Jurusan'],
            ['key' => 'profile_history', 'value' => '', 'group' => 'profil', 'type' => 'textarea', 'label' => 'Sejarah Jurusan'],
            ['key' => 'profile_vision', 'value' => '', 'group' => 'profil', 'type' => 'textarea', 'label' => 'Visi'],
            ['key' => 'profile_mission', 'value' => '', 'group' => 'profil', 'type' => 'textarea', 'label' => 'Misi (satu poin per baris)'],
            ['key' => 'profile_structure_image', 'value' => '', 'group' => 'profil', 'type' => 'image', 'label' => 'Struktur Organisasi'],

            ['key' => 'spmb_open', 'value' => '1', 'group' => 'spmb', 'type' => 'boolean', 'label' => 'SPMB Dibuka'],
            ['key' => 'spmb_url', 'value' => '', 'group' => 'spmb', 'type' => 'text', 'label' => 'Link Aplikasi SPMB'],
            ['key' => 'spmb_info', 'value' => 'Pendaftaran SPMB (Sistem Penerimaan Murid Baru) Tahun Pelajaran [TAHUN AJARAN] dibuka. Kuota terbatas.', 'group' => 'spmb', 'type' => 'textarea', 'label' => 'Info SPMB'],
        ];
        foreach ($settings as $s) {
            // Sebagian kunci (whatsapp, stat_*) sudah dibuat oleh migration;
            // updateOrInsert membuat seeder aman diulang.
            DB::table('settings')->updateOrInsert(
                ['key' => $s['key']],
                array_merge($s, [
                    'description' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $categories = [
            ['name' => 'Berita', 'slug' => 'berita', 'description' => 'Berita terbaru jurusan TKR', 'color' => '#1D5BF0', 'sort_order' => 0],
            ['name' => 'Pengumuman', 'slug' => 'pengumuman', 'description' => 'Pengumuman penting', 'color' => '#0F3FB8', 'sort_order' => 1],
            ['name' => 'Pengetahuan', 'slug' => 'pengetahuan', 'description' => 'Artikel pengetahuan otomotif', 'color' => '#34507F', 'sort_order' => 2],
            ['name' => 'Kegiatan', 'slug' => 'kegiatan', 'description' => 'Kegiatan siswa dan sekolah', 'color' => '#1D5BF0', 'sort_order' => 3],
            ['name' => 'Prestasi', 'slug' => 'prestasi', 'description' => 'Prestasi siswa dan sekolah', 'color' => '#0F3FB8', 'sort_order' => 4],
        ];
        foreach ($categories as $c) {
            DB::table('categories')->insert(array_merge($c, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Enam bidang kompetensi — teks persis mengikuti spesifikasi desain.
        $competencies = [
            ['title' => 'Engine Bensin & Diesel', 'slug' => 'engine-bensin-diesel', 'icon' => 'engine', 'short_description' => 'Tune up, overhaul mesin, sistem pelumasan, pendinginan, dan bahan bakar.', 'sort_order' => 1],
            ['title' => 'EFI & Diagnosis OBD', 'slug' => 'efi-diagnosis-obd', 'icon' => 'diagnosis', 'short_description' => 'Membaca data sensor dan kode kerusakan ECU menggunakan scan tool OBD-II.', 'sort_order' => 2],
            ['title' => 'Chassis: Rem, Suspensi & Kemudi', 'slug' => 'chassis-rem-suspensi-kemudi', 'icon' => 'chassis', 'short_description' => 'Sistem rem termasuk ABS, suspensi, power steering, serta spooring dan balancing.', 'sort_order' => 3],
            ['title' => 'Pemindah Tenaga', 'slug' => 'pemindah-tenaga', 'icon' => 'drivetrain', 'short_description' => 'Kopling, transmisi manual, transmisi otomatis dan CVT, poros propeler, serta diferensial.', 'sort_order' => 4],
            ['title' => 'Kelistrikan Bodi & AC', 'slug' => 'kelistrikan-bodi-ac', 'icon' => 'electrical', 'short_description' => 'Sistem starter, pengisian, penerangan, kelistrikan bodi, dan AC mobil.', 'sort_order' => 5],
            ['title' => 'Kendaraan Listrik & Hybrid', 'slug' => 'kendaraan-listrik-hybrid', 'icon' => 'ev', 'short_description' => 'Dasar baterai tegangan tinggi, motor listrik, dan prosedur keselamatan kerja EV.', 'sort_order' => 6],
        ];
        foreach ($competencies as $c) {
            DB::table('competencies')->insert(array_merge($c, [
                'description' => null,
                'image' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $programs = [
            ['title' => 'Teaching Factory', 'subtitle' => 'Belajar sambil mengerjakan unit nyata', 'icon' => 'building-storefront', 'description' => 'Siswa mengerjakan servis mobil pelanggan di bengkel praktik sekolah dengan standar industri.', 'sort_order' => 1],
            ['title' => 'Praktik Kerja Lapangan', 'subtitle' => 'Magang di dealer & bengkel mitra', 'icon' => 'briefcase', 'description' => 'Siswa kelas XII melaksanakan PKL di dealer resmi dan bengkel mitra jurusan.', 'sort_order' => 2],
            ['title' => 'Sertifikasi Kompetensi', 'subtitle' => 'Uji kompetensi keahlian', 'icon' => 'document-check', 'description' => 'Lulusan mengikuti uji kompetensi keahlian dan sertifikasi bersama mitra industri.', 'sort_order' => 3],
            ['title' => 'Bursa Kerja Khusus', 'subtitle' => 'Penyaluran kerja lulusan', 'icon' => 'users', 'description' => 'BKK menjembatani lulusan dengan dealer, bengkel, dan industri otomotif mitra.', 'sort_order' => 4],
        ];
        foreach ($programs as $p) {
            DB::table('programs')->insert(array_merge($p, [
                'image' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Empat sarana utama — kartu fasilitas di landing.
        $facilities = [
            ['name' => 'Bengkel Praktik & Car Lift', 'category' => 'Bengkel', 'description' => '[Jumlah] stall dan [jumlah] unit car lift.', 'sort_order' => 1],
            ['name' => 'Scan Tool & Engine Analyzer', 'category' => 'Alat', 'description' => 'Alat diagnosis untuk sistem EFI dan OBD-II.', 'sort_order' => 2],
            ['name' => 'Spooring & Balancing', 'category' => 'Alat', 'description' => 'Peralatan penyetelan roda dan kaki-kaki.', 'sort_order' => 3],
            ['name' => 'Trainer Kelistrikan & EV', 'category' => 'Lab', 'description' => 'Media praktik kelistrikan bodi dan kendaraan listrik.', 'sort_order' => 4],
        ];
        foreach ($facilities as $f) {
            DB::table('facilities')->insert(array_merge($f, [
                'image' => null,
                'quantity' => null,
                'condition' => 'Baik',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Placeholder logo mitra (diganti logo asli lewat Admin > Mitra).
        for ($i = 1; $i <= 5; $i++) {
            DB::table('partners')->insert([
                'name' => '[LOGO MITRA]',
                'logo' => null,
                'website' => null,
                'category' => 'Industri',
                'description' => null,
                'sort_order' => $i,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $catId = DB::table('categories')->where('slug', 'berita')->value('id');
        $userId = DB::table('users')->where('email', 'admin@tkr.sch.id')->value('id');
        DB::table('posts')->insert([
            'category_id' => $catId,
            'user_id' => $userId,
            'title' => 'Selamat Datang di Website Jurusan TKR',
            'slug' => 'selamat-datang-di-website-jurusan-tkr',
            'excerpt' => 'Website resmi Jurusan Teknik Kendaraan Ringan kini hadir sebagai sumber informasi jurusan, kegiatan, dan pendaftaran siswa baru.',
            'body' => '<p>Website resmi jurusan <strong>Teknik Kendaraan Ringan (TKR)</strong> telah diluncurkan.</p><p>Melalui website ini kami menyediakan informasi kompetensi keahlian, fasilitas praktik, prospek karier, berita kegiatan, serta informasi pendaftaran siswa baru.</p>',
            'status' => 'published',
            'published_at' => now(),
            'is_featured' => true,
            'allow_comments' => true,
            'views_count' => 0,
            'likes_count' => 0,
            'dislikes_count' => 0,
            'shares_count' => 0,
            'comments_count' => 0,
            'reading_time' => 2,
            'meta_title' => 'Selamat Datang di Website Jurusan TKR',
            'meta_description' => 'Website resmi Jurusan Teknik Kendaraan Ringan.',
            'focus_keyword' => 'teknik kendaraan ringan',
            'seo_score' => 80,
            'seo_report' => null,
            'og_image' => null,
            'canonical_url' => null,
            'cover_image' => null,
            'cover_alt' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
