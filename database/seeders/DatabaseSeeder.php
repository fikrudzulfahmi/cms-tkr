<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Data seeder resmi CMS TKR SMK Islam 1 Blitar.
 * Berisi data asli hasil ekstraksi tkr.smkislam1blitar.sch.id:
 * profil, guru/instruktur, sarana/fasilitas, program keahlian, prestasi, dan berita kegiatan.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Salin aset foto (guru, fasilitas, prestasi, berita) ke storage publik bila ada
        $seedAssetsDir = database_path('seeders/assets');
        $publicStorageDir = storage_path('app/public');
        if (File::isDirectory($seedAssetsDir)) {
            File::ensureDirectoryExists($publicStorageDir);
            File::copyDirectory($seedAssetsDir, $publicStorageDir);
        }

        // 2. Akun Administrator
        $adminPassword = (string) env('ADMIN_PASSWORD', '');
        if ($adminPassword === '') {
            $file = storage_path('app/admin-password.txt');
            if (File::exists($file)) {
                $adminPassword = trim(File::get($file));
            } else {
                $adminPassword = Str::password(16, symbols: false);
                File::put($file, $adminPassword . PHP_EOL);
                @chmod($file, 0600);
            }
        }

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@tkr.sch.id'],
            [
                'name' => 'Administrator TKR',
                'email_verified_at' => now(),
                'role' => 'admin',
                'password' => Hash::make($adminPassword),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $adminUser = DB::table('users')->where('email', 'admin@tkr.sch.id')->first();
        $adminId = $adminUser ? $adminUser->id : 1;

        // 3. Pengaturan Situs & Profil Resmi
        $settings = [
            ['key' => 'site_name', 'value' => 'TKR SMK Islam 1 Blitar', 'group' => 'umum', 'type' => 'text', 'label' => 'Nama Situs'],
            ['key' => 'site_tagline', 'value' => 'Success by Religious and Discipline', 'group' => 'umum', 'type' => 'text', 'label' => 'Tagline'],
            ['key' => 'site_logo', 'value' => '', 'group' => 'umum', 'type' => 'image', 'label' => 'Logo Situs'],
            ['key' => 'site_favicon', 'value' => '', 'group' => 'umum', 'type' => 'image', 'label' => 'Favicon'],
            ['key' => 'footer_text', 'value' => '© 2026 Jurusan Teknik Kendaraan Ringan — SMK Islam 1 Blitar', 'group' => 'umum', 'type' => 'textarea', 'label' => 'Teks Footer'],

            ['key' => 'school_name', 'value' => 'SMK Islam 1 Blitar', 'group' => 'profil', 'type' => 'text', 'label' => 'Nama Sekolah'],
            ['key' => 'department_name', 'value' => 'Teknik Kendaraan Ringan', 'group' => 'profil', 'type' => 'text', 'label' => 'Nama Jurusan'],
            ['key' => 'address', 'value' => 'Jl. Musi No. 6 Kauman, Kec. Kepanjenkidul, Kota Blitar, Jawa Timur 66117', 'group' => 'profil', 'type' => 'textarea', 'label' => 'Alamat'],
            ['key' => 'phone', 'value' => '0342-801450', 'group' => 'profil', 'type' => 'text', 'label' => 'Telepon'],
            ['key' => 'whatsapp', 'value' => '', 'group' => 'kontak', 'type' => 'text', 'label' => 'Nomor WhatsApp admin SPMB'],
            ['key' => 'email', 'value' => 'tkr.smkislam1blitar@gmail.com', 'group' => 'profil', 'type' => 'text', 'label' => 'Email'],
            ['key' => 'maps_embed', 'value' => '', 'group' => 'profil', 'type' => 'textarea', 'label' => 'Google Maps Embed'],
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/smkislam1blitar', 'group' => 'sosmed', 'type' => 'text', 'label' => 'Facebook URL'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/smkislam1blitar', 'group' => 'sosmed', 'type' => 'text', 'label' => 'Instagram URL'],
            ['key' => 'youtube_url', 'value' => 'https://youtube.com/@smkislam1blitartv', 'group' => 'sosmed', 'type' => 'text', 'label' => 'YouTube URL'],
            ['key' => 'tiktok_url', 'value' => '', 'group' => 'sosmed', 'type' => 'text', 'label' => 'TikTok URL'],

            ['key' => 'hero_title', 'value' => 'Teknik Kendaraan Ringan', 'group' => 'beranda', 'type' => 'text', 'label' => 'Hero Judul'],
            ['key' => 'hero_subtitle', 'value' => 'Kuasai teknologi mobil masa kini bersama SMK Islam 1 Blitar. Success by Religious and Discipline.', 'group' => 'beranda', 'type' => 'textarea', 'label' => 'Hero Subjudul'],
            ['key' => 'hero_bg', 'value' => '', 'group' => 'beranda', 'type' => 'image', 'label' => 'Hero Background'],
            ['key' => 'about_text', 'value' => '<p>Kompetensi Keahlian Teknik Kendaraan Ringan (TKR) SMK Islam 1 Blitar menyiapkan tenaga teknisi otomotif profesional yang menguasai perawatan dan perbaikan kendaraan roda empat, kelistrikan bodi, sistem EFI, hingga chasis. Kurikulum diselaraskan langsung dengan standar PT Astra Daihatsu Motor melalui program Pintar Bersama Daihatsu.</p>', 'group' => 'beranda', 'type' => 'html', 'label' => 'Teks Tentang Kami'],
            ['key' => 'about_image', 'value' => '', 'group' => 'beranda', 'type' => 'image', 'label' => 'Foto Tentang Kami'],
            ['key' => 'stat_students', 'value' => '741', 'group' => 'beranda', 'type' => 'text', 'label' => 'Statistik: siswa aktif'],
            ['key' => 'stat_graduates', 'value' => '1500+', 'group' => 'beranda', 'type' => 'text', 'label' => 'Statistik: alumni'],
            ['key' => 'stat_absorption', 'value' => '95%', 'group' => 'beranda', 'type' => 'text', 'label' => 'Statistik: lulusan terserap kerja'],

            ['key' => 'profile_head_name', 'value' => 'Ahmad Zunaidi, S.Pd', 'group' => 'profil', 'type' => 'text', 'label' => 'Nama Kepala Jurusan'],
            ['key' => 'profile_head_position', 'value' => 'Ketua Kompetensi Keahlian TKR', 'group' => 'profil', 'type' => 'text', 'label' => 'Jabatan Kepala Jurusan'],
            ['key' => 'profile_head_photo', 'value' => 'teachers/teacher_1_Ahmad_Zunaidi__S_Pd.png', 'group' => 'profil', 'type' => 'image', 'label' => 'Foto Kepala Jurusan'],
            ['key' => 'profile_speech', 'value' => "Assalamu’alaikum Warahmatullahi Wabarakatuh.

Selamat datang di website resmi Kompetensi Keahlian Teknik Kendaraan Ringan SMK Islam 1 Blitar. Program keahlian kami berkomitmen membekali setiap siswa dengan keterampilan otomotif terkini berstandar industri, berakhlak mulia, dan siap kerja.", 'group' => 'profil', 'type' => 'textarea', 'label' => 'Sambutan Kepala Jurusan'],
            ['key' => 'profile_history', 'value' => "Teknik Kendaraan Ringan (TKR) SMK Islam 1 Blitar berdiri untuk memenuhi kebutuhan tenaga kerja terampil di bidang otomotif roda empat. Bekerja sama dengan industri terkemuka seperti PT Astra Daihatsu Motor (Pintar Bersama Daihatsu) dan PT Kubota Indonesia, TKR terus berkembang menjadi jurusan unggulan dengan 18 rombel dan 741 siswa aktif.", 'group' => 'profil', 'type' => 'textarea', 'label' => 'Sejarah Jurusan'],
            ['key' => 'profile_vision', 'value' => "Terwujudnya Lulusan Kompetensi Keahlian Teknik Kendaraan Ringan yang Kompeten, Beriman, Berakhlak dan Kompetitif.", 'group' => 'profil', 'type' => 'textarea', 'label' => 'Visi'],
            ['key' => 'profile_mission', 'value' => "1. Meningkatkan keterampilan hidup mandiri siswa Teknik Kendaraan Ringan.
2. Memiliki kecerdasan dan pengetahuan memadai sesuai kebutuhan industri otomotif.
3. Menyelenggarakan pembelajaran berbasis praktik dan budaya industri (5S).
4. Menanamkan nilai religius dan kedisiplinan kerja (Success by Religious and Discipline).", 'group' => 'profil', 'type' => 'textarea', 'label' => 'Misi (satu poin per baris)'],
            ['key' => 'profile_structure_image', 'value' => 'settings/struktur_organisasi.jpg', 'group' => 'profil', 'type' => 'image', 'label' => 'Struktur Organisasi'],

            ['key' => 'spmb_open', 'value' => '1', 'group' => 'spmb', 'type' => 'boolean', 'label' => 'SPMB Dibuka'],
            ['key' => 'spmb_url', 'value' => 'https://smkislam1blitar.sch.id', 'group' => 'spmb', 'type' => 'text', 'label' => 'Link Aplikasi SPMB'],
            ['key' => 'spmb_info', 'value' => 'Pendaftaran SPMB (Sistem Penerimaan Murid Baru) SMK Islam 1 Blitar Tahun Pelajaran 2026/2027 dibuka. Kuota terbatas.', 'group' => 'spmb', 'type' => 'textarea', 'label' => 'Info SPMB'],
        ];
        foreach ($settings as $s) {
            DB::table('settings')->updateOrInsert(
                ['key' => $s['key']],
                array_merge($s, [
                    'description' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // 4. Kategori Berita
        $categories = [
            ['name' => 'Berita', 'slug' => 'berita', 'description' => 'Berita terbaru jurusan TKR', 'color' => '#1D5BF0', 'sort_order' => 1],
            ['name' => 'Prestasi', 'slug' => 'prestasi', 'description' => 'Prestasi siswa dan sekolah', 'color' => '#0F3FB8', 'sort_order' => 2],
            ['name' => 'Pengumuman', 'slug' => 'pengumuman', 'description' => 'Pengumuman penting', 'color' => '#34507F', 'sort_order' => 3],
            ['name' => 'Akademik', 'slug' => 'akademik', 'description' => 'Kurikulum dan pembelajaran', 'color' => '#1D5BF0', 'sort_order' => 4],
            ['name' => 'Kegiatan', 'slug' => 'kegiatan', 'description' => 'Kegiatan siswa dan praktik', 'color' => '#D97706', 'sort_order' => 5],
        ];
        foreach ($categories as $c) {
            DB::table('categories')->updateOrInsert(
                ['slug' => $c['slug']],
                array_merge($c, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // 5. Guru & Instruktur TKR Asli
        DB::table('teachers')->delete();
        DB::table('teachers')->insert([
            'name' => 'Ahmad Zunaidi, S.Pd',
            'position' => 'Ketua Kompetensi Keahlian TKR',
            'subject' => 'Kepala Jurusan TKR',
            'education' => 'S1 Pendidikan Teknik Mesin/Otomotif',
            'photo' => 'teachers/teacher_1_Ahmad_Zunaidi__S_Pd.png',
            'email' => null,
            'bio' => 'Ahmad Zunaidi, S.Pd mengabdi sebagai Ketua Kompetensi Keahlian TKR Kompetensi Keahlian Teknik Kendaraan Ringan SMK Islam 1 Blitar.',
            'sort_order' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('teachers')->insert([
            'name' => 'Drs. Sugito',
            'position' => 'Instruktur',
            'subject' => 'Produktif Teknik Kendaraan Ringan',
            'education' => 'Sarjana Teknik',
            'photo' => 'teachers/teacher_2_Drs__Sugito.png',
            'email' => null,
            'bio' => 'Drs. Sugito mengabdi sebagai Instruktur Kompetensi Keahlian Teknik Kendaraan Ringan SMK Islam 1 Blitar.',
            'sort_order' => 2,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('teachers')->insert([
            'name' => 'Zaenal Arifin, S.Pd',
            'position' => 'Instruktur',
            'subject' => 'Produktif Teknik Kendaraan Ringan',
            'education' => 'S1 Pendidikan Teknik Mesin/Otomotif',
            'photo' => 'teachers/teacher_3_Zaenal_Arifin__S_Pd.jpg',
            'email' => null,
            'bio' => 'Zaenal Arifin, S.Pd mengabdi sebagai Instruktur Kompetensi Keahlian Teknik Kendaraan Ringan SMK Islam 1 Blitar.',
            'sort_order' => 3,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('teachers')->insert([
            'name' => 'Sigit Tri Cahyono, S.Pd',
            'position' => 'Instruktur',
            'subject' => 'Produktif Teknik Kendaraan Ringan',
            'education' => 'S1 Pendidikan Teknik Mesin/Otomotif',
            'photo' => 'teachers/teacher_4_Sigit_Tri_Cahyono__S_Pd.png',
            'email' => null,
            'bio' => 'Sigit Tri Cahyono, S.Pd mengabdi sebagai Instruktur Kompetensi Keahlian Teknik Kendaraan Ringan SMK Islam 1 Blitar.',
            'sort_order' => 4,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('teachers')->insert([
            'name' => 'Harsono Ryohandoko, S.Pd',
            'position' => 'Instruktur',
            'subject' => 'Produktif Teknik Kendaraan Ringan',
            'education' => 'S1 Pendidikan Teknik Mesin/Otomotif',
            'photo' => 'teachers/teacher_5_Harsono_Ryohandoko__S_Pd.png',
            'email' => null,
            'bio' => 'Harsono Ryohandoko, S.Pd mengabdi sebagai Instruktur Kompetensi Keahlian Teknik Kendaraan Ringan SMK Islam 1 Blitar.',
            'sort_order' => 5,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('teachers')->insert([
            'name' => 'Rio Setiadi, S.Pd',
            'position' => 'Instruktur',
            'subject' => 'Produktif Teknik Kendaraan Ringan',
            'education' => 'S1 Pendidikan Teknik Mesin/Otomotif',
            'photo' => 'teachers/teacher_6_Rio_Setiadi__S_Pd.png',
            'email' => null,
            'bio' => 'Rio Setiadi, S.Pd mengabdi sebagai Instruktur Kompetensi Keahlian Teknik Kendaraan Ringan SMK Islam 1 Blitar.',
            'sort_order' => 6,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('teachers')->insert([
            'name' => 'Andre Ari Wibowo, S.Pd',
            'position' => 'Instruktur',
            'subject' => 'Produktif Teknik Kendaraan Ringan',
            'education' => 'S1 Pendidikan Teknik Mesin/Otomotif',
            'photo' => 'teachers/teacher_7_Andre_Ari_Wibowo__S_Pd.png',
            'email' => null,
            'bio' => 'Andre Ari Wibowo, S.Pd mengabdi sebagai Instruktur Kompetensi Keahlian Teknik Kendaraan Ringan SMK Islam 1 Blitar.',
            'sort_order' => 7,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('teachers')->insert([
            'name' => 'Wahyu Bintoro, S.Pd',
            'position' => 'Instruktur',
            'subject' => 'Produktif Teknik Kendaraan Ringan',
            'education' => 'S1 Pendidikan Teknik Mesin/Otomotif',
            'photo' => 'teachers/teacher_8_Wahyu_Bintoro__S_Pd.png',
            'email' => null,
            'bio' => 'Wahyu Bintoro, S.Pd mengabdi sebagai Instruktur Kompetensi Keahlian Teknik Kendaraan Ringan SMK Islam 1 Blitar.',
            'sort_order' => 8,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('teachers')->insert([
            'name' => 'Billy Yusuf Prihandono, S.Pd',
            'position' => 'Instruktur',
            'subject' => 'Produktif Teknik Kendaraan Ringan',
            'education' => 'S1 Pendidikan Teknik Mesin/Otomotif',
            'photo' => 'teachers/teacher_9_Billy_Yusuf_Prihandono__S_Pd.png',
            'email' => null,
            'bio' => 'Billy Yusuf Prihandono, S.Pd mengabdi sebagai Instruktur Kompetensi Keahlian Teknik Kendaraan Ringan SMK Islam 1 Blitar.',
            'sort_order' => 9,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Prestasi Siswa TKR Asli
        DB::table('achievements')->delete();
        DB::table('achievements')->insert([
            'title' => 'Tim TKR SMK Islam 1 Blitar Raih Juara 1 Unjuk Karya dan Kompetensi Siswa SMK Ma’arif NU & SMK Pondok Pesantren Jawa Timur 2025',
            'student_name' => 'Tim Siswa TKR SMEKISA',
            'level' => 'Jawa Timur',
            'rank' => 'Juara 1',
            'organizer' => 'PW LP Ma\'arif NU & Ponpes Jawa Timur',
            'year' => '2025',
            'achieved_at' => '2025-10-16',
            'description' => 'Blitar, 2025 — Prestasi membanggakan kembali diraih oleh keluarga besar SMK Islam 1 Blitar (SMEKISA). Tim dari jurusan Teknik Kendaraan Ringan (TKR) berhasil meraih Juara 1 dalam ajang Unjuk Karya dan Kompetensi Siswa SMK Ma’arif NU dan SMK Pondok Pesantren se-Jawa Timur Tahun 2025. Pencapaian gemilang ini menjadi bukti nyata bahwa kerja keras, ketekunan, dan',
            'image' => 'achievements/ach_1164_tim-tkr-smk-islam-1-blitar-raih-juara-1-unjuk-karya-dan-kompetensi-siswa-smk-maarif-nu-smk-pondok-pesantren-jawa-timur-2025.jpeg',
            'is_featured' => true,
            'sort_order' => 0,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('achievements')->insert([
            'title' => 'Siswa SMK Islam 1 Blitar Raih Juara II LKS Nasional 2025 Bidang Automobile Technology',
            'student_name' => 'Shena Azi Saputra',
            'level' => 'Nasional',
            'rank' => 'Juara 2',
            'organizer' => 'Dinas Pendidikan / BPTI Kemendikbudristek',
            'year' => '2025',
            'achieved_at' => '2025-08-15',
            'description' => 'Prestasi membanggakan kembali ditorehkan oleh SMK Islam 1 Blitar di kancah nasional. Pada ajang Lomba Kompetensi Siswa (LKS) Pendidikan Menengah Tingkat Nasional 2025, siswa SMK Islam 1 Blitar, Shena Azi Saputra, berhasil meraih Juara II pada bidang lomba Automobile Technology atau Teknologi Otomotif Mobil. Kompetisi bergengsi ini mempertemukan siswa-siswa terbaik dari seluruh provinsi di Indonesia',
            'image' => 'achievements/ach_1156_siswa-smk-islam-1-blitar-raih-juara-ii-lks-nasional-2025-bidang-automobile-technology.webp',
            'is_featured' => true,
            'sort_order' => 0,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('achievements')->insert([
            'title' => 'Bima Saputra Ramadhan Raih Juara 1 LKS Car Painting Tingkat Provinsi Jawa Timur 2025',
            'student_name' => 'Bima Saputra Ramadhan',
            'level' => 'Jawa Timur',
            'rank' => 'Juara 1',
            'organizer' => 'Dinas Pendidikan / BPTI Kemendikbudristek',
            'year' => '2025',
            'achieved_at' => '2025-04-26',
            'description' => 'Blitar, 25 April 2025 — SMK Islam 1 Blitar kembali mencetak prestasi membanggakan di ajang bergengsi tingkat provinsi. Bima Saputra Ramadhan, siswa kelas XI TKR 1, sukses meraih Juara 1 dalam Lomba Kompetensi Siswa (LKS) Dikmen Provinsi Jawa Timur Tahun 2025, untuk bidang lomba Car Painting. Kompetisi ini merupakan ajang tahunan yang diikuti oleh para',
            'image' => 'achievements/ach_1143_bima-saputra-ramadhan-raih-juara-1-lks-car-painting-tingkat-provinsi-jawa-timur-2025.jpg',
            'is_featured' => true,
            'sort_order' => 0,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('achievements')->insert([
            'title' => 'SMK Islam 1 Blitar Ukir Prestasi! Shena Juara 1 Lomba Automobile Technology Tingkat Jatim',
            'student_name' => 'Shena Azi Saputra',
            'level' => 'Jawa Timur',
            'rank' => 'Juara 1',
            'organizer' => 'Dinas Pendidikan',
            'year' => '2025',
            'achieved_at' => '2025-04-26',
            'description' => 'Blitar, 25 April 2025 — Kabar membanggakan datang dari SMK Islam 1 Blitar. Shena Azi Saputra, siswa kelas XII TKR 6, berhasil meraih Juara 1 dalam ajang Lomba Kompetensi Siswa (LKS) Tingkat Provinsi Jawa Timur 2025 pada bidang Automobile Technology. Kompetisi ini berlangsung sejak tanggal 21 hingga 25 April 2025, dan diikuti oleh peserta terbaik',
            'image' => 'achievements/ach_1140_smk-islam-1-blitar-ukir-prestasi-shena-juara-1-lomba-automobile-technology-tingkat-jatim.jpg',
            'is_featured' => false,
            'sort_order' => 0,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('achievements')->insert([
            'title' => 'Rico Julian Saputra Berjuang di LKS Provinsi Jatim 2025 Bidang Autobody Repair',
            'student_name' => 'Rico Julian Saputra',
            'level' => 'Jawa Timur',
            'rank' => 'Finalis',
            'organizer' => 'Dinas Pendidikan / BPTI Kemendikbudristek',
            'year' => '2025',
            'achieved_at' => '2025-04-22',
            'description' => 'Haloooo sobat Smekisa!! Hari ini, salah satu putra terbaik SMK Islam 1 Blitar, Ananda Rico Julian Saputra, tengah berjuang dalam ajang Lomba Kompetensi Siswa (LKS) Tingkat Provinsi Jawa Timur pada bidang lomba Autobody Repair. Mari kita semua memberikan do’a restu dan dukungan terbaik untuk Rico agar diberikan kelancaran, kemudahan, ketenangan hati, serta mampu tampil dengan',
            'image' => 'achievements/ach_1122_rico-julian-saputra-berjuang-di-lks-provinsi-jatim-2025-bidang-autobody-repair.jpg',
            'is_featured' => false,
            'sort_order' => 0,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('achievements')->insert([
            'title' => 'Juara 1 Lomba Kompetensi Siswa (LKS) Automotive Technology tingkat SMK Ma’arif NU dan SMK PonPes se-Jawa Timur Tahun 2025',
            'student_name' => 'Shena Azi Saputra',
            'level' => 'Jawa Timur',
            'rank' => 'Juara 1',
            'organizer' => 'PW LP Ma\'arif NU & Ponpes Jawa Timur',
            'year' => '2025',
            'achieved_at' => '2025-02-05',
            'description' => 'Selamat kepada Shena Azi Saputra, siswa kelas XII TKR 6 SMK Islam 1 Blitar, atas pencapaiannya sebagai Juara 1 Lomba Kompetensi Siswa (LKS) Automotive Technology tingkat SMK Ma’arif NU dan SMK PonPes se-Jawa Timur Tahun 2025. Prestasi ini membuktikan bahwa ketekunan, kerja keras, dan dedikasi Shena dalam bidang otomotif telah menghasilkan hasil yang membanggakan bagi',
            'image' => 'achievements/ach_1083_juara-1-lomba-kompetensi-siswa-lks-automotive-technology-tingkat-smk-maarif-nu-dan-smk-ponpes-se-jawa-timur-tahun-2025.jpeg',
            'is_featured' => false,
            'sort_order' => 0,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('achievements')->insert([
            'title' => 'Juara 2 Lomba Kompetensi Siswa (LKS) Automotive Technology tingkat SMK Ma’arif NU dan SMK PonPes se-Jawa Timur Tahun 2025',
            'student_name' => 'Shena Azi Saputra',
            'level' => 'Jawa Timur',
            'rank' => 'Juara 2',
            'organizer' => 'PW LP Ma\'arif NU & Ponpes Jawa Timur',
            'year' => '2025',
            'achieved_at' => '2025-02-05',
            'description' => 'Selamat kepada Marvelano Lutfi A, siswa kelas X TKR 6 SMK Islam 1 Blitar, atas pencapaiannya sebagai Juara 2 Lomba Kompetensi Siswa (LKS) Automotive Technology tingkat SMK Ma’arif NU dan SMK PonPes se-Jawa Timur Tahun 2025. Prestasi ini membuktikan bahwa ketekunan, kerja keras, dan dedikasi Shena dalam bidang otomotif telah menghasilkan hasil yang membanggakan bagi',
            'image' => 'achievements/ach_1089_juara-2-lomba-kompetensi-siswa-lks-automotive-technology-tingkat-smk-maarif-nu-dan-smk-ponpes-se-jawa-timur-tahun-2025.jpeg',
            'is_featured' => false,
            'sort_order' => 0,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 7. Fasilitas & Sarana Bengkel TKR Asli
        DB::table('facilities')->delete();
        DB::table('facilities')->insert([
            'name' => '11 Unit Mobil Praktik Siswa',
            'category' => 'Peralatan Praktik',
            'description' => 'Tersedia 11 unit mobil berbagai tipe untuk praktik siswa meliputi pemeriksaan mesin, sistem kelistrikan, chasis, dan sistem EFI.',
            'image' => 'facilities/fac_praktik_1.jpg',
            'quantity' => 11,
            'condition' => 'Baik / Siap Pakai',
            'sort_order' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('facilities')->insert([
            'name' => 'Peralatan Spooring 3D Komputer',
            'category' => 'Peralatan Khusus',
            'description' => 'Perangkat wheel alignment 3D computerized presisi tinggi standar industri untuk penyelarasan roda kendaraan ringan.',
            'image' => 'facilities/fac_praktik_2.jpg',
            'quantity' => 1,
            'condition' => 'Sangat Baik',
            'sort_order' => 2,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('facilities')->insert([
            'name' => 'Wheel Balancing Machine',
            'category' => 'Peralatan Khusus',
            'description' => 'Mesin balancing dinamis roda mobil untuk memastikan putaran roda stabil dan seimbang.',
            'image' => null,
            'quantity' => 2,
            'condition' => 'Baik',
            'sort_order' => 3,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('facilities')->insert([
            'name' => 'Tyre Changer Otomatis',
            'category' => 'Peralatan Khusus',
            'description' => 'Alat pembuka dan pemasang ban otomatis untuk efisiensi dan keamanan pekerjaan roda ban.',
            'image' => null,
            'quantity' => 2,
            'condition' => 'Baik',
            'sort_order' => 4,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('facilities')->insert([
            'name' => 'Car Lift (Two-Post & Four-Post Lift)',
            'category' => 'Peralatan Utama',
            'description' => 'Car lift hidrolik untuk memudahkan pekerjaan bagian bawah chasis, suspensi, dan transmisi kendaraan.',
            'image' => null,
            'quantity' => 4,
            'condition' => 'Baik / Terkalibrasi',
            'sort_order' => 5,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('facilities')->insert([
            'name' => 'Bengkel Standar Industri DUDI (Astra Daihatsu)',
            'category' => 'Ruang Praktik',
            'description' => 'Bengkel praktik luas dengan layout 5S standar industri PT Astra Daihatsu Motor lengkap dengan tool set perbengkelan.',
            'image' => null,
            'quantity' => 1,
            'condition' => 'Standar DUDI',
            'sort_order' => 6,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 8. Program Unggulan TKR Asli
        DB::table('programs')->delete();
        DB::table('programs')->insert([
            'title' => 'Daihatsu Class Program',
            'subtitle' => 'Pintar Bersama Daihatsu (PT Astra Daihatsu Motor)',
            'icon' => 'wrench',
            'description' => 'Program kelas industri hasil kerjasama resmi antara SMK Islam 1 Blitar dengan PT Astra Daihatsu Motor (ADM). Kurikulum diselaraskan langsung dengan kebutuhan industri otomotif, pelatihan guru bersertifikat, dan peluang rekrutmen kerja langsung di jaringan Daihatsu.',
            'image' => null,
            'sort_order' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('programs')->insert([
            'title' => 'Kubota Class Program',
            'subtitle' => 'Kerjasama PT Kubota Indonesia',
            'icon' => 'cog',
            'description' => 'Program keahlian spesialisasi mesin diesel dan alat transportasi pertanian bekerjasama dengan PT Kubota Indonesia, memperluas kompetensi lulusan di sektor alat berat dan mesin industri.',
            'image' => null,
            'sort_order' => 2,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('programs')->insert([
            'title' => 'Unit Produksi & Jasa (UPJ) Otomotif',
            'subtitle' => 'Layanan Service & Perawatan Berkala Kendaraan',
            'icon' => 'truck',
            'description' => 'Bengkel layanan masyarakat dan unit bisnis sekolah yang melayani tune up, spooring-balancing, ganti oli, dan perbaikan kendaraan ringan oleh siswa di bawah bimbingan instruktur profesional.',
            'image' => null,
            'sort_order' => 3,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('programs')->insert([
            'title' => 'Praktik Kerja Lapangan (PKL) Industri',
            'subtitle' => 'Jejaring Bengkel Resmi & Industri Nasional',
            'icon' => 'briefcase',
            'description' => 'Penempatan praktik kerja lapangan di puluhan bengkel resmi terkemuka (Daihatsu, Toyota, Honda, Mitsubishi) dan bengkel rekanan berstandar DUDI untuk mematangkan pengalaman kerja nyata siswa.',
            'image' => null,
            'sort_order' => 4,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 9. Kompetensi Keahlian
        DB::table('competencies')->delete();
        $competencies = [
            ['title' => 'Engine Bensin & Diesel', 'slug' => 'engine-bensin-diesel', 'icon' => 'engine', 'short_description' => 'Tune up, overhaul mesin, sistem pelumasan, pendinginan, dan bahan bakar.', 'sort_order' => 1],
            ['title' => 'EFI & Diagnosis OBD-II', 'slug' => 'efi-diagnosis-obd', 'icon' => 'diagnosis', 'short_description' => 'Membaca data sensor dan kode kerusakan ECU menggunakan scan tool OBD-II.', 'sort_order' => 2],
            ['title' => 'Chassis: Rem, Suspensi & Kemudi', 'slug' => 'chassis-rem-suspensi-kemudi', 'icon' => 'chassis', 'short_description' => 'Sistem rem termasuk ABS, suspensi, power steering, serta spooring dan balancing.', 'sort_order' => 3],
            ['title' => 'Pemindah Tenaga & Transmisi', 'slug' => 'pemindah-tenaga', 'icon' => 'drivetrain', 'short_description' => 'Kopling, transmisi manual, transmisi otomatis dan CVT, poros propeler, serta diferensial.', 'sort_order' => 4],
            ['title' => 'Kelistrikan Bodi & AC Mobil', 'slug' => 'kelistrikan-bodi-ac', 'icon' => 'electrical', 'short_description' => 'Sistem starter, pengisian, penerangan, kelistrikan bodi, dan AC mobil.', 'sort_order' => 5],
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

        // 10. Mitra DUDI Resmi
        DB::table('partners')->delete();
        $partners = [
            ['name' => 'PT Astra Daihatsu Motor', 'category' => 'Industri', 'description' => 'Kelas Industri Pintar Bersama Daihatsu', 'sort_order' => 1],
            ['name' => 'PT Kubota Indonesia', 'category' => 'Industri', 'description' => 'Kelas Khusus Mesin Diesel & Mesin Pertanian', 'sort_order' => 2],
            ['name' => 'Bengkel Resmi Daihatsu', 'category' => 'Bengkel Rekanan', 'description' => 'Mitra PKL & Magang Industri Siswa', 'sort_order' => 3],
            ['name' => 'Bengkel Resmi Toyota', 'category' => 'Bengkel Rekanan', 'description' => 'Mitra Penempatan PKL Siswa', 'sort_order' => 4],
            ['name' => 'Bengkel Resmi Mitsubishi', 'category' => 'Bengkel Rekanan', 'description' => 'Mitra Penempatan PKL Siswa', 'sort_order' => 5],
        ];
        foreach ($partners as $part) {
            DB::table('partners')->insert(array_merge($part, [
                'logo' => null,
                'website' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 11. Hero Slider
        DB::table('sliders')->delete();
        $sliders = [
            ['title' => 'Teknik Kendaraan Ringan SMK Islam 1 Blitar', 'subtitle' => 'Kelas Industri Kerjasama PT Astra Daihatsu Motor & PT Kubota Indonesia', 'button_text' => 'Pelajari Profil', 'button_url' => '#tentang', 'sort_order' => 1],
            ['title' => 'SPMB 2026/2027 Telah Dibuka', 'subtitle' => 'Raih Masa Depan Gemilang Bersama Jurusan TKR SMK Islam 1 Blitar', 'button_text' => 'Daftar Sekarang', 'button_url' => '/spmb', 'sort_order' => 2],
        ];
        foreach ($sliders as $s) {
            DB::table('sliders')->insert(array_merge($s, [
                'image' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 12. Berita & Artikel Asli
        DB::table('posts')->delete();
        $catBeritaId = DB::table('categories')->where('slug', 'berita')->value('id') ?? 1;
        $catPrestasiId = DB::table('categories')->where('slug', 'prestasi')->value('id') ?? $catBeritaId;

        DB::table('posts')->insert([
            'category_id' => $catPrestasiId,
            'user_id' => $adminId,
            'title' => 'Tim TKR SMK Islam 1 Blitar Raih Juara 1 Unjuk Karya dan Kompetensi Siswa SMK Ma’arif NU & SMK Pondok Pesantren Jawa Timur 2025',
            'slug' => 'tim-tkr-smk-islam-1-blitar-raih-juara-1-unjuk-karya-dan-kompetensi-siswa-smk-maarif-nu-smk-pondok-pesantren-jawa-timur-2025',
            'excerpt' => 'Blitar, 2025 — Prestasi membanggakan kembali diraih oleh keluarga besar SMK Islam 1 Blitar (SMEKISA). Tim dari jurusan Teknik Kendaraan Ringan (TKR) berhasil meraih Juara 1 dalam ajang Unjuk Karya dan Kompetensi Siswa SMK Ma’arif NU dan SMK Pondok Pesantren se-Jawa Timur Tahun 2025. Pencapaian gemil',
            'body' => '<p><p style="text-align: justify;" data-start="224" data-end="529"><strong data-start="224" data-end="240">Blitar, 2025</strong> — Prestasi membanggakan kembali diraih oleh keluarga besar <strong data-start="300" data-end="332">SMK Islam 1 Blitar (SMEKISA)</strong>. Tim dari <strong data-start="343" data-end="384">jurusan Teknik Kendaraan Ringan (TKR)</strong> berhasil meraih <strong data-start="401" data-end="412">Juara 1</strong> dalam ajang <strong data-start="425" data-end="526">Unjuk Karya dan Kompetensi Siswa SMK Ma’arif NU dan SMK Pondok Pesantren se-Jawa Timur Tahun 2025</strong>.</p>
<p style="text-align: justify;" data-start="531" data-end="872">Pencapaian gemilang ini menjadi bukti nyata bahwa <strong data-start="581" data-end="641">kerja keras, ketekunan, dan semangat belajar yang tinggi</strong> mampu membawa hasil yang luar biasa. Tim TKR SMK Islam 1 Blitar menunjukkan kemampuan terbaiknya dalam bidang otomotif dengan menampilkan keterampilan, inovasi, dan profesionalisme yang sesuai dengan standar kompetensi industri.</p>
<p style="text-align: justify;" data-start="874" data-end="1195">Keberhasilan ini tidak hanya menjadi kebanggaan bagi jurusan TKR, tetapi juga mengharumkan nama <strong data-start="970" data-end="992">SMK Islam 1 Blitar</strong> di tingkat provinsi. Melalui prestasi ini, sekolah membuktikan komitmennya dalam mencetak lulusan yang unggul, berkarakter, serta siap bersaing di dunia kerja maupun kompetisi keahlian tingkat lanjut.</p>
<p style="text-align: justify;" data-start="1197" data-end="1488">Kepala SMK Islam 1 Blitar menyampaikan apresiasi tinggi atas capaian tersebut. “Kami sangat bangga dengan semangat dan perjuangan siswa-siswa kami. Prestasi ini adalah hasil dari kerja sama, disiplin, dan dedikasi seluruh tim serta bimbingan guru-guru pembimbing yang luar biasa,” ujarnya.</p>
<p style="text-align: justify;" data-start="1490" data-end="1854">Prestasi ini diharapkan menjadi motivasi bagi seluruh siswa SMEKISA untuk terus <strong data-start="1570" data-end="1627">berinovasi, berkarya, dan menunjukkan potensi terbaik</strong> di berbagai bidang keahlian. Dengan menjunjung tinggi nilai <em data-start="1688" data-end="1723">Skill by Religious and Discipline</em>, SMK Islam 1 Blitar akan terus berkomitmen mencetak generasi yang kompeten, berakhlak, dan siap menghadapi tantangan masa depan.</p>
<p style="text-align: justify;" data-start="1856" data-end="1981"><strong data-start="1856" data-end="1872">SMK… Bisa!</strong><br data-start="1872" data-end="1875" /><strong data-start="1875" data-end="1895">SMEKISA… Jaya!</strong><br data-start="1895" data-end="1898" /><strong data-start="1898" data-end="1957">Safety Commitment… Safety Adalah Tanggung Jawab Saya!</strong><br data-start="1957" data-end="1960" /><strong data-start="1960" data-end="1981">Yosh! Yosh! Yosh!</strong></p>
<p data-start="1856" data-end="1981"><img loading="lazy" decoding="async" class="alignnone wp-image-1165 size-large" src="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/10/Unjuk-Karya-dan-Kompetensi-Siswa-SMK-Maarif-NU-SMK-Pondok-Pesantren-Jawa-Timur-2025-1-1024x576.jpeg" alt="" width="1024" height="576" srcset="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/10/Unjuk-Karya-dan-Kompetensi-Siswa-SMK-Maarif-NU-SMK-Pondok-Pesantren-Jawa-Timur-2025-1-980x551.jpeg 980w, https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/10/Unjuk-Karya-dan-Kompetensi-Siswa-SMK-Maarif-NU-SMK-Pondok-Pesantren-Jawa-Timur-2025-1-480x270.jpeg 480w" sizes="(min-width: 0px) and (max-width: 480px) 480px, (min-width: 481px) and (max-width: 980px) 980px, (min-width: 981px) 1024px, 100vw" /></p>
<p data-start="1856" data-end="1981"><img loading="lazy" decoding="async" class="alignnone wp-image-9704 size-large" src="https://smkislam1blitar.sch.id/wp-content/uploads/2025/10/Unjuk-Karya-dan-Kompetensi-Siswa-SMK-Maarif-NU-SMK-Pondok-Pesantren-Jawa-Timur-2025-2-1024x768.jpeg" alt="" width="1024" height="768" /> <img loading="lazy" decoding="async" class="alignnone wp-image-9705 size-large" src="https://smkislam1blitar.sch.id/wp-content/uploads/2025/10/Unjuk-Karya-dan-Kompetensi-Siswa-SMK-Maarif-NU-SMK-Pondok-Pesantren-Jawa-Timur-2025-3-1024x768.jpeg" alt="" width="1024" height="768" /></p></p>',
            'status' => 'published',
            'published_at' => '2025-10-16 07:56:49',
            'is_featured' => true,
            'allow_comments' => true,
            'views_count' => rand(150, 600),
            'likes_count' => rand(10, 45),
            'dislikes_count' => 0,
            'shares_count' => rand(5, 20),
            'comments_count' => 0,
            'reading_time' => 3,
            'meta_title' => 'Tim TKR SMK Islam 1 Blitar Raih Juara 1 Unjuk Karya dan Kompetensi Siswa SMK Ma’arif NU & SMK Pondok Pesantren Jawa Timur 2025',
            'meta_description' => 'Blitar, 2025 — Prestasi membanggakan kembali diraih oleh keluarga besar SMK Islam 1 Blitar (SMEKISA). Tim dari jurusan Teknik Kendaraan Ringan (TKR) berhasil me',
            'focus_keyword' => 'teknik kendaraan ringan smk islam 1 blitar',
            'seo_score' => 90,
            'seo_report' => null,
            'og_image' => 'posts/post_1164_tim-tkr-smk-islam-1-blitar-raih-juara-1-unjuk-karya-dan-kompetensi-siswa-smk-maarif-nu-smk-pondok-pesantren-jawa-timur-2025.jpeg',
            'canonical_url' => null,
            'cover_image' => 'posts/post_1164_tim-tkr-smk-islam-1-blitar-raih-juara-1-unjuk-karya-dan-kompetensi-siswa-smk-maarif-nu-smk-pondok-pesantren-jawa-timur-2025.jpeg',
            'cover_alt' => 'Tim TKR SMK Islam 1 Blitar Raih Juara 1 Unjuk Karya dan Kompetensi Siswa SMK Ma’arif NU & SMK Pondok Pesantren Jawa Timur 2025',
            'created_at' => '2025-10-16 07:56:49',
            'updated_at' => now(),
        ]);
        DB::table('posts')->insert([
            'category_id' => $catPrestasiId,
            'user_id' => $adminId,
            'title' => 'Siswa SMK Islam 1 Blitar Raih Juara II LKS Nasional 2025 Bidang Automobile Technology',
            'slug' => 'siswa-smk-islam-1-blitar-raih-juara-ii-lks-nasional-2025-bidang-automobile-technology',
            'excerpt' => 'Prestasi membanggakan kembali ditorehkan oleh SMK Islam 1 Blitar di kancah nasional. Pada ajang Lomba Kompetensi Siswa (LKS) Pendidikan Menengah Tingkat Nasional 2025, siswa SMK Islam 1 Blitar, Shena Azi Saputra, berhasil meraih Juara II pada bidang lomba Automobile Technology atau Teknologi Otomoti',
            'body' => '<p><p style="text-align: justify;" data-start="194" data-end="524">Prestasi membanggakan kembali ditorehkan oleh <strong data-start="240" data-end="262">SMK Islam 1 Blitar</strong> di kancah nasional. Pada ajang <strong data-start="294" data-end="368">Lomba Kompetensi Siswa (LKS) Pendidikan Menengah Tingkat Nasional 2025</strong>, siswa SMK Islam 1 Blitar, <strong data-start="396" data-end="417">Shena Azi Saputra</strong>, berhasil meraih <strong data-start="435" data-end="447">Juara II</strong> pada bidang lomba <strong data-start="466" data-end="491">Automobile Technology</strong> atau Teknologi Otomotif Mobil.</p>
<p style="text-align: justify;" data-start="526" data-end="908">Kompetisi bergengsi ini mempertemukan siswa-siswa terbaik dari seluruh provinsi di Indonesia untuk menunjukkan keterampilan, ketelitian, dan kemampuan teknis di bidang masing-masing. Dalam perlombaan yang berlangsung ketat, Shena mampu menunjukkan kompetensi tinggi, kreativitas, serta ketepatan kerja, sehingga mengantarkannya menjadi salah satu yang terbaik di tingkat nasional.</p>
<p style="text-align: justify;" data-start="910" data-end="1219">Atas prestasi ini, Shena berhak membawa pulang <strong data-start="957" data-end="973">medali perak</strong>, <strong data-start="975" data-end="996">piala penghargaan</strong>, serta hadiah sebesar <strong data-start="1019" data-end="1036">Rp 20.000.000</strong>. Keberhasilan ini tidak terlepas dari kerja keras Shena selama masa persiapan, bimbingan intensif dari guru pembimbing, serta dukungan penuh dari keluarga besar SMK Islam 1 Blitar.</p>
<p style="text-align: justify;" data-start="1221" data-end="1537">Kepala SMK Islam 1 Blitar menyampaikan apresiasi yang tinggi atas prestasi yang diraih. “Kami bangga atas pencapaian Shena yang berhasil mengharumkan nama sekolah di tingkat nasional. Semoga ini menjadi inspirasi bagi seluruh siswa untuk terus berprestasi dan mengembangkan keterampilan sesuai bidangnya,” ujarnya.</p>
<p style="text-align: justify;" data-start="1539" data-end="1851">Prestasi ini menjadi bukti bahwa SMK Islam 1 Blitar mampu mencetak generasi muda yang unggul di bidang vokasi, selaras dengan semangat <em data-start="1674" data-end="1709">Skill by Religious and Discipline</em>. Semoga capaian ini memotivasi siswa lainnya untuk mengukir prestasi di masa depan, baik di tingkat daerah, nasional, maupun internasional.</p>
<p data-start="1539" data-end="1851"><img loading="lazy" decoding="async" class="alignnone wp-image-1157 size-large" src="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/08/juara-2-LKS-Nasional-6-819x1024.webp" alt="" width="819" height="1024" /></p>
<p data-start="1539" data-end="1851"><img loading="lazy" decoding="async" class="alignnone wp-image-1158 size-large" src="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/08/juara-2-LKS-Nasional-1-1024x768.jpg" alt="" width="1024" height="768" srcset="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/08/juara-2-LKS-Nasional-1-980x735.jpg 980w, https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/08/juara-2-LKS-Nasional-1-480x360.jpg 480w" sizes="(min-width: 0px) and (max-width: 480px) 480px, (min-width: 481px) and (max-width: 980px) 980px, (min-width: 981px) 1024px, 100vw" /> <img loading="lazy" decoding="async" class="alignnone wp-image-1159 size-large" src="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/08/juara-2-LKS-Nasional-2-1024x683.jpg" alt="" width="1024" height="683" srcset="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/08/juara-2-LKS-Nasional-2-980x654.jpg 980w, https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/08/juara-2-LKS-Nasional-2-480x320.jpg 480w" sizes="(min-width: 0px) and (max-width: 480px) 480px, (min-width: 481px) and (max-width: 980px) 980px, (min-width: 981px) 1024px, 100vw" /> <img loading="lazy" decoding="async" class="alignnone wp-image-1160 size-large" src="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/08/juara-2-LKS-Nasional-3-1024x774.jpg" alt="" width="1024" height="774" srcset="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/08/juara-2-LKS-Nasional-3-980x741.jpg 980w, https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/08/juara-2-LKS-Nasional-3-480x363.jpg 480w" sizes="(min-width: 0px) and (max-width: 480px) 480px, (min-width: 481px) and (max-width: 980px) 980px, (min-width: 981px) 1024px, 100vw" /> <img loading="lazy" decoding="async" class="alignnone wp-image-1161 size-large" src="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/08/juara-2-LKS-Nasional-5-683x1024.jpg" alt="" width="683" height="1024" /></p></p>',
            'status' => 'published',
            'published_at' => '2025-08-15 09:29:27',
            'is_featured' => true,
            'allow_comments' => true,
            'views_count' => rand(150, 600),
            'likes_count' => rand(10, 45),
            'dislikes_count' => 0,
            'shares_count' => rand(5, 20),
            'comments_count' => 0,
            'reading_time' => 3,
            'meta_title' => 'Siswa SMK Islam 1 Blitar Raih Juara II LKS Nasional 2025 Bidang Automobile Technology',
            'meta_description' => 'Prestasi membanggakan kembali ditorehkan oleh SMK Islam 1 Blitar di kancah nasional. Pada ajang Lomba Kompetensi Siswa (LKS) Pendidikan Menengah Tingkat Nasiona',
            'focus_keyword' => 'teknik kendaraan ringan smk islam 1 blitar',
            'seo_score' => 90,
            'seo_report' => null,
            'og_image' => 'posts/post_1156_siswa-smk-islam-1-blitar-raih-juara-ii-lks-nasional-2025-bidang-automobile-technology.webp',
            'canonical_url' => null,
            'cover_image' => 'posts/post_1156_siswa-smk-islam-1-blitar-raih-juara-ii-lks-nasional-2025-bidang-automobile-technology.webp',
            'cover_alt' => 'Siswa SMK Islam 1 Blitar Raih Juara II LKS Nasional 2025 Bidang Automobile Technology',
            'created_at' => '2025-08-15 09:29:27',
            'updated_at' => now(),
        ]);
        DB::table('posts')->insert([
            'category_id' => $catPrestasiId,
            'user_id' => $adminId,
            'title' => 'Bima Saputra Ramadhan Raih Juara 1 LKS Car Painting Tingkat Provinsi Jawa Timur 2025',
            'slug' => 'bima-saputra-ramadhan-raih-juara-1-lks-car-painting-tingkat-provinsi-jawa-timur-2025',
            'excerpt' => 'Blitar, 25 April 2025 — SMK Islam 1 Blitar kembali mencetak prestasi membanggakan di ajang bergengsi tingkat provinsi. Bima Saputra Ramadhan, siswa kelas XI TKR 1, sukses meraih Juara 1 dalam Lomba Kompetensi Siswa (LKS) Dikmen Provinsi Jawa Timur Tahun 2025, untuk bidang lomba Car Painting. Kompeti',
            'body' => '<p><br />
			<br />
				Blitar, 25 April 2025 — SMK Islam 1 Blitar kembali mencetak prestasi membanggakan di ajang bergengsi tingkat provinsi. <strong>Bima Saputra Ramadhan</strong>, siswa kelas <strong>XI TKR 1</strong>, sukses meraih <strong>Juara 1</strong> dalam <strong>Lomba Kompetensi Siswa (LKS) Dikmen Provinsi Jawa Timur Tahun 2025</strong>, untuk bidang lomba <strong>Car Painting</strong>.</p>
<p>Kompetisi ini merupakan ajang tahunan yang diikuti oleh para siswa terbaik dari seluruh SMK di Jawa Timur. Dengan keterampilan yang luar biasa dalam pengecatan kendaraan, ketelitian, dan kreativitas yang tinggi, Bima berhasil menarik perhatian para juri dan keluar sebagai yang terbaik di antara para peserta.</p>
<p>Selama proses persiapan hingga pelaksanaan lomba, Bima dibimbing oleh <strong>Bapak Andre Ari Wibowo, S.Pd.</strong>, yang telah dengan sabar dan tekun mendampingi dalam mengasah kemampuan teknis dan mental bertanding.</p>
<p><strong>Kepala SMK Islam 1 Blitar</strong>, Bapak <strong>Drs. H. Gigih Widiyanto</strong>, menyampaikan rasa bangga atas prestasi tersebut. <em>“Ini adalah pencapaian yang luar biasa. Kami sangat mengapresiasi kerja keras Bima dan pembimbingnya. Semoga pencapaian ini menjadi awal langkah menuju prestasi di tingkat nasional, bahkan internasional. Doa kami menyertai, semoga lancar dan meraih hasil terbaik di setiap tahap selanjutnya.”</em></p>
<p>Kemenangan ini menjadi inspirasi bagi seluruh siswa untuk terus berusaha, berprestasi, dan menunjukkan bahwa generasi muda SMK mampu bersaing di tingkat yang lebih tinggi.<br />
			<br />
		</p>',
            'status' => 'published',
            'published_at' => '2025-04-26 01:07:11',
            'is_featured' => false,
            'allow_comments' => true,
            'views_count' => rand(150, 600),
            'likes_count' => rand(10, 45),
            'dislikes_count' => 0,
            'shares_count' => rand(5, 20),
            'comments_count' => 0,
            'reading_time' => 3,
            'meta_title' => 'Bima Saputra Ramadhan Raih Juara 1 LKS Car Painting Tingkat Provinsi Jawa Timur 2025',
            'meta_description' => 'Blitar, 25 April 2025 — SMK Islam 1 Blitar kembali mencetak prestasi membanggakan di ajang bergengsi tingkat provinsi. Bima Saputra Ramadhan, siswa kelas XI TKR',
            'focus_keyword' => 'teknik kendaraan ringan smk islam 1 blitar',
            'seo_score' => 90,
            'seo_report' => null,
            'og_image' => 'posts/post_1143_bima-saputra-ramadhan-raih-juara-1-lks-car-painting-tingkat-provinsi-jawa-timur-2025.jpg',
            'canonical_url' => null,
            'cover_image' => 'posts/post_1143_bima-saputra-ramadhan-raih-juara-1-lks-car-painting-tingkat-provinsi-jawa-timur-2025.jpg',
            'cover_alt' => 'Bima Saputra Ramadhan Raih Juara 1 LKS Car Painting Tingkat Provinsi Jawa Timur 2025',
            'created_at' => '2025-04-26 01:07:11',
            'updated_at' => now(),
        ]);
        DB::table('posts')->insert([
            'category_id' => $catPrestasiId,
            'user_id' => $adminId,
            'title' => 'SMK Islam 1 Blitar Ukir Prestasi! Shena Juara 1 Lomba Automobile Technology Tingkat Jatim',
            'slug' => 'smk-islam-1-blitar-ukir-prestasi-shena-juara-1-lomba-automobile-technology-tingkat-jatim',
            'excerpt' => 'Blitar, 25 April 2025 — Kabar membanggakan datang dari SMK Islam 1 Blitar. Shena Azi Saputra, siswa kelas XII TKR 6, berhasil meraih Juara 1 dalam ajang Lomba Kompetensi Siswa (LKS) Tingkat Provinsi Jawa Timur 2025 pada bidang Automobile Technology. Kompetisi ini berlangsung sejak tanggal 21 hingga ',
            'body' => '<p><div class="et_pb_section et_pb_section_0 et_section_regular" >
				
				
				
				
				
				
				<div class="et_pb_row et_pb_row_0">
								<div class="et_pb_column et_pb_column_4_4 et_pb_column_0  et_pb_css_mix_blend_mode_passthrough et-last-child">
				
				
				
				
				<div class="et_pb_module et_pb_text et_pb_text_0  et_pb_text_align_left et_pb_bg_layout_light">
				
				
				
				
				<div class="et_pb_text_inner"><p style="text-align: justify;">Blitar, 25 April 2025 — Kabar membanggakan datang dari SMK Islam 1 Blitar. <strong>Shena Azi Saputra</strong>, siswa kelas <strong>XII TKR 6</strong>, berhasil meraih <strong>Juara 1</strong> dalam ajang <strong>Lomba Kompetensi Siswa (LKS) Tingkat Provinsi Jawa Timur 2025</strong> pada bidang <strong>Automobile Technology</strong>.</p>
<p style="text-align: justify;">Kompetisi ini berlangsung sejak tanggal <strong>21 hingga 25 April 2025</strong>, dan diikuti oleh peserta terbaik dari seluruh kabupaten/kota se-Jawa Timur. Shena menunjukkan kemampuan luar biasa dalam bidang otomotif dan berhasil mengungguli para pesaingnya. Ia dibimbing oleh <strong>Bapak Rio Setiadi, S.Pd.</strong>, guru produktif TKR yang telah mendampingi Shena dari awal hingga akhir lomba.</p>
<p style="text-align: justify;"><strong>Kepala SMK Islam 1 Blitar</strong>, Bapak Drs. H. Gigih Widiyanto, turut menyampaikan apresiasinya. <em>“Kami sangat bangga dengan prestasi yang diraih oleh Shena. Ini membuktikan bahwa siswa SMK Islam 1 Blitar mampu bersaing di tingkat provinsi. Semoga di ajang tingkat nasional nanti, Shena dapat kembali meraih hasil terbaik dan bisa mengharumkan nama sekolah di tingkat nasional bahkan internasional.”</em></p>
<p style="text-align: justify;">Dengan prestasi ini, Shena akan melanjutkan perjuangannya di <strong>LKS Tingkat Nasional</strong>. Seluruh keluarga besar SMK Islam 1 Blitar mendoakan agar proses selanjutnya berjalan <strong>lancar</strong>, diberikan <strong>hasil terbaik</strong>, dan semoga bisa <strong>Go Internasional</strong>!</p>
<p style="text-align: justify;">Selamat dan sukses selalu untuk Shena Azi Saputra dan Bapak Rio Setiadi. Terus semangat mengukir prestasi!</p></div>
			</div>
			</div>			
				
				
				
				
			</div>		
				
				
			</div></p>',
            'status' => 'published',
            'published_at' => '2025-04-26 00:59:31',
            'is_featured' => false,
            'allow_comments' => true,
            'views_count' => rand(150, 600),
            'likes_count' => rand(10, 45),
            'dislikes_count' => 0,
            'shares_count' => rand(5, 20),
            'comments_count' => 0,
            'reading_time' => 3,
            'meta_title' => 'SMK Islam 1 Blitar Ukir Prestasi! Shena Juara 1 Lomba Automobile Technology Tingkat Jatim',
            'meta_description' => 'Blitar, 25 April 2025 — Kabar membanggakan datang dari SMK Islam 1 Blitar. Shena Azi Saputra, siswa kelas XII TKR 6, berhasil meraih Juara 1 dalam ajang Lomba K',
            'focus_keyword' => 'teknik kendaraan ringan smk islam 1 blitar',
            'seo_score' => 90,
            'seo_report' => null,
            'og_image' => 'posts/post_1140_smk-islam-1-blitar-ukir-prestasi-shena-juara-1-lomba-automobile-technology-tingkat-jatim.jpg',
            'canonical_url' => null,
            'cover_image' => 'posts/post_1140_smk-islam-1-blitar-ukir-prestasi-shena-juara-1-lomba-automobile-technology-tingkat-jatim.jpg',
            'cover_alt' => 'SMK Islam 1 Blitar Ukir Prestasi! Shena Juara 1 Lomba Automobile Technology Tingkat Jatim',
            'created_at' => '2025-04-26 00:59:31',
            'updated_at' => now(),
        ]);
        DB::table('posts')->insert([
            'category_id' => $catPrestasiId,
            'user_id' => $adminId,
            'title' => 'Rico Julian Saputra Berjuang di LKS Provinsi Jatim 2025 Bidang Autobody Repair',
            'slug' => 'rico-julian-saputra-berjuang-di-lks-provinsi-jatim-2025-bidang-autobody-repair',
            'excerpt' => 'Haloooo sobat Smekisa!! Hari ini, salah satu putra terbaik SMK Islam 1 Blitar, Ananda Rico Julian Saputra, tengah berjuang dalam ajang Lomba Kompetensi Siswa (LKS) Tingkat Provinsi Jawa Timur pada bidang lomba Autobody Repair. Mari kita semua memberikan do’a restu dan dukungan terbaik untuk Rico aga',
            'body' => '<p><p class="" data-start="186" data-end="244"><strong data-start="217" data-end="244">Haloooo sobat Smekisa!!</strong></p>
<p class="" data-start="246" data-end="460">Hari ini, salah satu putra terbaik SMK Islam 1 Blitar, <strong data-start="301" data-end="331">Ananda Rico Julian Saputra</strong>, tengah <strong data-start="340" data-end="421">berjuang dalam ajang Lomba Kompetensi Siswa (LKS) Tingkat Provinsi Jawa Timur</strong> pada <strong data-start="427" data-end="459">bidang lomba Autobody Repair</strong>.</p>
<p class="" data-start="462" data-end="670">Mari kita semua memberikan <strong data-start="489" data-end="524">do’a restu dan dukungan terbaik</strong> untuk Rico agar diberikan <strong data-start="551" data-end="593">kelancaran, kemudahan, ketenangan hati</strong>, serta mampu tampil dengan <strong data-start="621" data-end="638">hasil terbaik</strong> dalam perlombaan ini. Aamiin 🤲</p>
<p class="" data-start="672" data-end="845">Partisipasi dalam LKS bukan hanya ajang adu keterampilan, tetapi juga sebagai sarana membentuk karakter, memperluas pengalaman, dan membangun semangat kompetitif yang sehat.</p>
<p class="" data-start="847" data-end="965"><strong data-start="847" data-end="872">Semangat terus, Rico!</strong> Tunjukkan kemampuan terbaikmu dan harumkan nama baik SMK Islam 1 Blitar di tingkat provinsi!</p>
<p class="" data-start="967" data-end="1113"><strong data-start="967" data-end="984">SMK… Bisa!!</strong><br data-start="984" data-end="987" /><strong data-start="987" data-end="1008">SMEKISA… Jaya!!</strong><br data-start="1008" data-end="1011" /><strong data-start="1011" data-end="1035">Safety Commitment…</strong><br data-start="1035" data-end="1038" /><strong data-start="1038" data-end="1077">Safety Adalah Tanggung Jawab Saya!!</strong><br data-start="1077" data-end="1080" /><strong data-start="1080" data-end="1113">Yosh…!! Yosh…!! Yosh…!!</strong></p></p>',
            'status' => 'published',
            'published_at' => '2025-04-22 06:03:24',
            'is_featured' => false,
            'allow_comments' => true,
            'views_count' => rand(150, 600),
            'likes_count' => rand(10, 45),
            'dislikes_count' => 0,
            'shares_count' => rand(5, 20),
            'comments_count' => 0,
            'reading_time' => 3,
            'meta_title' => 'Rico Julian Saputra Berjuang di LKS Provinsi Jatim 2025 Bidang Autobody Repair',
            'meta_description' => 'Haloooo sobat Smekisa!! Hari ini, salah satu putra terbaik SMK Islam 1 Blitar, Ananda Rico Julian Saputra, tengah berjuang dalam ajang Lomba Kompetensi Siswa (L',
            'focus_keyword' => 'teknik kendaraan ringan smk islam 1 blitar',
            'seo_score' => 90,
            'seo_report' => null,
            'og_image' => 'posts/post_1122_rico-julian-saputra-berjuang-di-lks-provinsi-jatim-2025-bidang-autobody-repair.jpg',
            'canonical_url' => null,
            'cover_image' => 'posts/post_1122_rico-julian-saputra-berjuang-di-lks-provinsi-jatim-2025-bidang-autobody-repair.jpg',
            'cover_alt' => 'Rico Julian Saputra Berjuang di LKS Provinsi Jatim 2025 Bidang Autobody Repair',
            'created_at' => '2025-04-22 06:03:24',
            'updated_at' => now(),
        ]);
        DB::table('posts')->insert([
            'category_id' => $catBeritaId,
            'user_id' => $adminId,
            'title' => 'Pelaksanaan Uji Kompetensi Keahlian (UKK) Jurusan Teknik Kendaraan Ringan SMK Islam 1 Blitar Tahun Ajaran 2024/2025',
            'slug' => 'pelaksanaan-uji-kompetensi-keahlian-ukk-jurusan-teknik-kendaraan-ringan-smk-islam-1-blitar-tahun-ajaran-2024-2025',
            'excerpt' => 'Blitar, 14 Februari 2025 – Jurusan Teknik Kendaraan Ringan (TKR) SMK Islam 1 Blitar melaksanakan Uji Kompetensi Keahlian (UKK) bagi siswa kelas XII mulai tanggal 14 Februari hingga 26 Februari 2025. UKK ini menjadi bagian penting dalam menilai kompetensi siswa sebelum mereka lulus dan memasuki dunia',
            'body' => '<p><div class="et_pb_section et_pb_section_1 et_section_regular" >
				
				
				
				
				
				
				<div class="et_pb_row et_pb_row_1">
				<div class="et_pb_column et_pb_column_4_4 et_pb_column_1  et_pb_css_mix_blend_mode_passthrough et-last-child">
				
				
				
				
				<div class="et_pb_module et_pb_text et_pb_text_1  et_pb_text_align_left et_pb_bg_layout_light">
				
				
				
				
				<div class="et_pb_text_inner"><p style="text-align: justify;" data-start="221" data-end="599">Blitar, 14 Februari 2025 – <strong data-start="248" data-end="308">Jurusan Teknik Kendaraan Ringan (TKR) SMK Islam 1 Blitar</strong> melaksanakan <strong data-start="322" data-end="355">Uji Kompetensi Keahlian (UKK)</strong> bagi siswa kelas XII mulai tanggal <strong data-start="391" data-end="430">14 Februari hingga 26 Februari 2025</strong>. UKK ini menjadi bagian penting dalam menilai kompetensi siswa sebelum mereka lulus dan memasuki dunia kerja atau melanjutkan pendidikan ke jenjang yang lebih tinggi.</p>
<p style="text-align: justify;" data-start="601" data-end="1022">Materi yang diujikan dalam UKK TKR tahun ini mengacu pada standar yang telah ditetapkan oleh <strong data-start="694" data-end="774">Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi (Kemendikbudristek)</strong> serta mengikuti kebutuhan <strong data-start="801" data-end="837">Dunia Kerja dan Asosiasi Profesi</strong>. Dengan standar ini, siswa diuji dalam berbagai keterampilan teknik otomotif, termasuk perawatan dan perbaikan kendaraan ringan sesuai dengan perkembangan industri otomotif saat ini.</p>
<p style="text-align: justify;" data-start="1024" data-end="1369">Penguji dalam UKK ini merupakan kombinasi dari <strong data-start="1071" data-end="1148">penguji eksternal yang berasal dari Dunia Usaha dan Dunia Industri (DuDi)</strong> serta <strong data-start="1155" data-end="1240">penguji internal dari bapak dan ibu guru produktif jurusan TKR SMK Islam 1 Blitar</strong>. Adanya penguji eksternal bertujuan untuk memastikan bahwa penilaian UKK dilakukan secara profesional sesuai standar industri.</p>
<p style="text-align: justify;" data-start="1371" data-end="1758">Kepala Program Keahlian TKR, Ahmad Zunaidi, S.Pd., menyampaikan bahwa UKK ini menjadi kesempatan bagi siswa untuk menunjukkan keterampilan mereka yang telah dipelajari selama di sekolah. “UKK bukan sekadar ujian kelulusan, tetapi juga menjadi pembuktian kesiapan siswa untuk bersaing di dunia kerja. Kami berharap para siswa dapat memanfaatkan kesempatan ini sebaik mungkin,” ujarnya.</p>
<p style="text-align: justify;" data-start="1760" data-end="1986">Dengan terlaksananya UKK ini, diharapkan lulusan jurusan TKR SMK Islam 1 Blitar memiliki <strong data-start="1849" data-end="1875">kompetensi yang unggul</strong> dan <strong data-start="1880" data-end="1943">siap menghadapi dunia industri otomotif maupun berwirausaha</strong> di bidang perbengkelan kendaraan ringan.</p>
<p style="text-align: justify;" data-start="1988" data-end="2066">Semoga UKK tahun ini berjalan dengan lancar dan sukses bagi seluruh peserta.</p>
<p data-start="1988" data-end="2066"><img loading="lazy" decoding="async" class="alignnone wp-image-1076 size-large" src="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/02/UKK-JURUSAN-TKR-1-1024x683.jpg" alt="" width="1024" height="683" srcset="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/02/UKK-JURUSAN-TKR-1-980x653.jpg 980w, https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/02/UKK-JURUSAN-TKR-1-480x320.jpg 480w" sizes="(min-width: 0px) and (max-width: 480px) 480px, (min-width: 481px) and (max-width: 980px) 980px, (min-width: 981px) 1024px, 100vw" /> <img loading="lazy" decoding="async" class="alignnone wp-image-1077 size-large" src="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/02/UKK-JURUSAN-TKR-2-1024x683.jpg" alt="" width="1024" height="683" srcset="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/02/UKK-JURUSAN-TKR-2-980x653.jpg 980w, https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/02/UKK-JURUSAN-TKR-2-480x320.jpg 480w" sizes="(min-width: 0px) and (max-width: 480px) 480px, (min-width: 481px) and (max-width: 980px) 980px, (min-width: 981px) 1024px, 100vw" /> <img loading="lazy" decoding="async" class="alignnone wp-image-1078 size-large" src="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/02/UKK-JURUSAN-TKR-3-1024x683.jpg" alt="" width="1024" height="683" srcset="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/02/UKK-JURUSAN-TKR-3-980x653.jpg 980w, https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/02/UKK-JURUSAN-TKR-3-480x320.jpg 480w" sizes="(min-width: 0px) and (max-width: 480px) 480px, (min-width: 481px) and (max-width: 980px) 980px, (min-width: 981px) 1024px, 100vw" /> <img loading="lazy" decoding="async" class="alignnone wp-image-1079 size-large" src="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/02/UKK-JURUSAN-TKR-4-1024x683.jpg" alt="" width="1024" height="683" srcset="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/02/UKK-JURUSAN-TKR-4-980x653.jpg 980w, https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/02/UKK-JURUSAN-TKR-4-480x320.jpg 480w" sizes="(min-width: 0px) and (max-width: 480px) 480px, (min-width: 481px) and (max-width: 980px) 980px, (min-width: 981px) 1024px, 100vw" /> <img loading="lazy" decoding="async" class="alignnone wp-image-1080 size-large" src="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/02/UKK-JURUSAN-TKR-5-1024x683.jpg" alt="" width="1024" height="683" srcset="https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/02/UKK-JURUSAN-TKR-5-980x653.jpg 980w, https://tkr.smkislam1blitar.sch.id/wp-content/uploads/2025/02/UKK-JURUSAN-TKR-5-480x320.jpg 480w" sizes="(min-width: 0px) and (max-width: 480px) 480px, (min-width: 481px) and (max-width: 980px) 980px, (min-width: 981px) 1024px, 100vw" /></p></div>
			</div>
			</div>
				
				
				
				
			</div>
				
				
			</div></p>',
            'status' => 'published',
            'published_at' => '2025-02-24 07:48:14',
            'is_featured' => false,
            'allow_comments' => true,
            'views_count' => rand(150, 600),
            'likes_count' => rand(10, 45),
            'dislikes_count' => 0,
            'shares_count' => rand(5, 20),
            'comments_count' => 0,
            'reading_time' => 3,
            'meta_title' => 'Pelaksanaan Uji Kompetensi Keahlian (UKK) Jurusan Teknik Kendaraan Ringan SMK Islam 1 Blitar Tahun Ajaran 2024/2025',
            'meta_description' => 'Blitar, 14 Februari 2025 – Jurusan Teknik Kendaraan Ringan (TKR) SMK Islam 1 Blitar melaksanakan Uji Kompetensi Keahlian (UKK) bagi siswa kelas XII mulai tangga',
            'focus_keyword' => 'teknik kendaraan ringan smk islam 1 blitar',
            'seo_score' => 90,
            'seo_report' => null,
            'og_image' => 'posts/post_1075_pelaksanaan-uji-kompetensi-keahlian-ukk-jurusan-teknik-kendaraan-ringan-smk-islam-1-blitar-tahun-ajaran-2024-2025.jpg',
            'canonical_url' => null,
            'cover_image' => 'posts/post_1075_pelaksanaan-uji-kompetensi-keahlian-ukk-jurusan-teknik-kendaraan-ringan-smk-islam-1-blitar-tahun-ajaran-2024-2025.jpg',
            'cover_alt' => 'Pelaksanaan Uji Kompetensi Keahlian (UKK) Jurusan Teknik Kendaraan Ringan SMK Islam 1 Blitar Tahun Ajaran 2024/2025',
            'created_at' => '2025-02-24 07:48:14',
            'updated_at' => now(),
        ]);
        DB::table('posts')->insert([
            'category_id' => $catPrestasiId,
            'user_id' => $adminId,
            'title' => 'Juara 1 Lomba Kompetensi Siswa (LKS) Automotive Technology tingkat SMK Ma’arif NU dan SMK PonPes se-Jawa Timur Tahun 2025',
            'slug' => 'juara-1-lomba-kompetensi-siswa-lks-automotive-technology-tingkat-smk-maarif-nu-dan-smk-ponpes-se-jawa-timur-tahun-2025',
            'excerpt' => 'Selamat kepada Shena Azi Saputra, siswa kelas XII TKR 6 SMK Islam 1 Blitar, atas pencapaiannya sebagai Juara 1 Lomba Kompetensi Siswa (LKS) Automotive Technology tingkat SMK Ma’arif NU dan SMK PonPes se-Jawa Timur Tahun 2025. Prestasi ini membuktikan bahwa ketekunan, kerja keras, dan dedikasi Shena ',
            'body' => '<p><div class="et_pb_section et_pb_section_2 et_section_regular" >
				
				
				
				
				
				
				<div class="et_pb_row et_pb_row_2">
								<div class="et_pb_column et_pb_column_4_4 et_pb_column_2  et_pb_css_mix_blend_mode_passthrough et-last-child">
				
				
				
				
				<div class="et_pb_module et_pb_text et_pb_text_2  et_pb_text_align_left et_pb_bg_layout_light">
				
				
				
				
				<div class="et_pb_text_inner">Selamat kepada Shena Azi Saputra, siswa kelas XII TKR 6 SMK Islam 1 Blitar, atas pencapaiannya sebagai Juara 1 Lomba Kompetensi Siswa (LKS) Automotive Technology tingkat SMK Ma’arif NU dan SMK PonPes se-Jawa Timur Tahun 2025. Prestasi ini membuktikan bahwa ketekunan, kerja keras, dan dedikasi Shena dalam bidang otomotif telah menghasilkan hasil yang membanggakan bagi dirinya, keluarga, dan sekolah.</p>
<p>Keberhasilan ini juga menjadi kebanggaan besar bagi seluruh warga SMEKISA. Prestasi ini tidak hanya menunjukkan kualitas pendidikan di SMEKISA, tetapi juga menjadi motivasi bagi siswa-siswa lainnya untuk terus berprestasi dan mengharumkan nama sekolah di berbagai kompetisi. Kami percaya bahwa pencapaian ini adalah awal dari kesuksesan Shena di masa depan.</p>
<p>Kami berharap Shena dapat terus mengembangkan potensinya dan menginspirasi siswa-siswa lainnya untuk berani bermimpi dan berusaha keras untuk mencapainya. Sekali lagi, selamat atas prestasi gemilang ini, dan semoga SMEKISA terus mencetak generasi unggul yang berprestasi dan berkarakter!</p>
<p>SMK… Bisa!!<br />
SMEKISA… Jaya!!<br />
Safety Commitment…<br />
Safety Adalah Tanggung Jawab Saya!!<br />
Yosh…!! Yosh…!! Yosh…!!</p>
<p>Wassalamu’alaikum Wr. Wb.</p>
<p>#smkislam1blitar #sekolahpusatkeunggulan #smkbisasmkhebat #smkpusatkeunggulan #smekisa</div>
			</div>
			</div>			
				
				
				
				
			</div>		
				
				
			</div></p>',
            'status' => 'published',
            'published_at' => '2025-02-05 08:02:49',
            'is_featured' => false,
            'allow_comments' => true,
            'views_count' => rand(150, 600),
            'likes_count' => rand(10, 45),
            'dislikes_count' => 0,
            'shares_count' => rand(5, 20),
            'comments_count' => 0,
            'reading_time' => 3,
            'meta_title' => 'Juara 1 Lomba Kompetensi Siswa (LKS) Automotive Technology tingkat SMK Ma’arif NU dan SMK PonPes se-Jawa Timur Tahun 2025',
            'meta_description' => 'Selamat kepada Shena Azi Saputra, siswa kelas XII TKR 6 SMK Islam 1 Blitar, atas pencapaiannya sebagai Juara 1 Lomba Kompetensi Siswa (LKS) Automotive Technolog',
            'focus_keyword' => 'teknik kendaraan ringan smk islam 1 blitar',
            'seo_score' => 90,
            'seo_report' => null,
            'og_image' => 'posts/post_1083_juara-1-lomba-kompetensi-siswa-lks-automotive-technology-tingkat-smk-maarif-nu-dan-smk-ponpes-se-jawa-timur-tahun-2025.jpeg',
            'canonical_url' => null,
            'cover_image' => 'posts/post_1083_juara-1-lomba-kompetensi-siswa-lks-automotive-technology-tingkat-smk-maarif-nu-dan-smk-ponpes-se-jawa-timur-tahun-2025.jpeg',
            'cover_alt' => 'Juara 1 Lomba Kompetensi Siswa (LKS) Automotive Technology tingkat SMK Ma’arif NU dan SMK PonPes se-Jawa Timur Tahun 2025',
            'created_at' => '2025-02-05 08:02:49',
            'updated_at' => now(),
        ]);
        DB::table('posts')->insert([
            'category_id' => $catPrestasiId,
            'user_id' => $adminId,
            'title' => 'Juara 2 Lomba Kompetensi Siswa (LKS) Automotive Technology tingkat SMK Ma’arif NU dan SMK PonPes se-Jawa Timur Tahun 2025',
            'slug' => 'juara-2-lomba-kompetensi-siswa-lks-automotive-technology-tingkat-smk-maarif-nu-dan-smk-ponpes-se-jawa-timur-tahun-2025',
            'excerpt' => 'Selamat kepada Marvelano Lutfi A, siswa kelas X TKR 6 SMK Islam 1 Blitar, atas pencapaiannya sebagai Juara 2 Lomba Kompetensi Siswa (LKS) Automotive Technology tingkat SMK Ma’arif NU dan SMK PonPes se-Jawa Timur Tahun 2025. Prestasi ini membuktikan bahwa ketekunan, kerja keras, dan dedikasi Shena da',
            'body' => '<p>Selamat kepada Marvelano Lutfi A, siswa kelas X TKR 6 SMK Islam 1 Blitar, atas pencapaiannya sebagai Juara 2 Lomba Kompetensi Siswa (LKS) Automotive Technology tingkat SMK Ma’arif NU dan SMK PonPes se-Jawa Timur Tahun 2025. Prestasi ini membuktikan bahwa ketekunan, kerja keras, dan dedikasi Shena dalam bidang otomotif telah menghasilkan hasil yang membanggakan bagi dirinya, keluarga, dan sekolah. Keberhasilan ini juga menjadi kebanggaan besar bagi seluruh warga SMEKISA. Prestasi ini tidak hanya menunjukkan kualitas pendidikan di SMEKISA, tetapi juga menjadi motivasi bagi siswa-siswa lainnya untuk terus berprestasi dan mengharumkan nama sekolah di berbagai kompetisi. Kami percaya bahwa pencapaian ini adalah awal dari kesuksesan Shena di masa depan. Kami berharap Shena dapat terus mengembangkan potensinya dan menginspirasi siswa-siswa lainnya untuk berani bermimpi dan berusaha keras untuk mencapainya. Sekali lagi, selamat atas prestasi gemilang ini, dan semoga SMEKISA terus mencetak generasi unggul yang berprestasi dan berkarakter! SMK… Bisa!! SMEKISA… Jaya!! Safety Commitment… Safety Adalah Tanggung Jawab Saya!! Yosh…!! Yosh…!! Yosh…!! Wassalamu’alaikum Wr. Wb. #smkislam1blitar #sekolahpusatkeunggulan #smkbisasmkhebat #smkpusatkeunggulan #smekisa</p>',
            'status' => 'published',
            'published_at' => '2025-02-05 04:35:59',
            'is_featured' => false,
            'allow_comments' => true,
            'views_count' => rand(150, 600),
            'likes_count' => rand(10, 45),
            'dislikes_count' => 0,
            'shares_count' => rand(5, 20),
            'comments_count' => 0,
            'reading_time' => 3,
            'meta_title' => 'Juara 2 Lomba Kompetensi Siswa (LKS) Automotive Technology tingkat SMK Ma’arif NU dan SMK PonPes se-Jawa Timur Tahun 2025',
            'meta_description' => 'Selamat kepada Marvelano Lutfi A, siswa kelas X TKR 6 SMK Islam 1 Blitar, atas pencapaiannya sebagai Juara 2 Lomba Kompetensi Siswa (LKS) Automotive Technology ',
            'focus_keyword' => 'teknik kendaraan ringan smk islam 1 blitar',
            'seo_score' => 90,
            'seo_report' => null,
            'og_image' => 'posts/post_1089_juara-2-lomba-kompetensi-siswa-lks-automotive-technology-tingkat-smk-maarif-nu-dan-smk-ponpes-se-jawa-timur-tahun-2025.jpeg',
            'canonical_url' => null,
            'cover_image' => 'posts/post_1089_juara-2-lomba-kompetensi-siswa-lks-automotive-technology-tingkat-smk-maarif-nu-dan-smk-ponpes-se-jawa-timur-tahun-2025.jpeg',
            'cover_alt' => 'Juara 2 Lomba Kompetensi Siswa (LKS) Automotive Technology tingkat SMK Ma’arif NU dan SMK PonPes se-Jawa Timur Tahun 2025',
            'created_at' => '2025-02-05 04:35:59',
            'updated_at' => now(),
        ]);

        // 13. Tag Otomotif
        DB::table('tags')->delete();
        $tags = ['Automobile Technology', 'LKS Nasional', 'LKS Jatim', 'Car Painting', 'Autobody Repair', 'UKK Otomotif', 'Daihatsu', 'Kubota', 'Engine', 'Chassis'];
        foreach ($tags as $t) {
            DB::table('tags')->insert([
                'name' => $t,
                'slug' => Str::slug($t),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
