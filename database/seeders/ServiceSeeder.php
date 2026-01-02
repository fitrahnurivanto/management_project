<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\ServicePackage;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // A. Marketplace
        $marketplace = ServiceCategory::create([
            'name' => 'Marketplace',
            'slug' => 'marketplace',
            'description' => 'Layanan untuk optimasi dan manajemen marketplace',
            'icon' => 'shopping-cart',
            'is_active' => true,
            'display_order' => 1,
        ]);

        // 1. Jasa Iklan Marketplace
        $iklanMarketplace = Service::create([
            'category_id' => $marketplace->id,
            'name' => 'Jasa Iklan Marketplace',
            'slug' => 'iklan-marketplace',
            'description' => 'Iklan yang powerfull dimulai dari pengelolaan yang baik. Creativemu menyediakan layanan untuk mengiklankan produk anda di marketplace.',
            'base_price' => 1500000,
            'features' => ['Riset keyword', 'Setup campaign', 'Monitoring & report'],
            'icon' => 'ad',
            'is_active' => true,
            'display_order' => 1,
        ]);

        ServicePackage::create([
            'service_id' => $iklanMarketplace->id, 
            'name' => 'Paket Basic', 
            'price' => 1500000, 
            'duration' => 30, 
            'features' => [
                'Market Ads',
                'Setup dan manajemen iklan 2 sku',
                'Diskusi tujuan iklan',
                'Penentuan budget',
                'Optimasi judul & keyword untuk iklan',
                'Monitoring & evaluasi mingguan',
                'Anggaran iklan disesuaikan (dari klien)',
                'Waktu Pengelolaan 30 Hari'
            ], 
            'display_order' => 1
        ]);
        
        ServicePackage::create([
            'service_id' => $iklanMarketplace->id, 
            'name' => 'Paket Pro', 
            'price' => 2500000, 
            'duration' => 30, 
            'features' => [
                'Market Ads',
                'Setup dan manajemen iklan hingga 5 sku',
                'Diskusi tujuan iklan',
                'Penentuan budget',
                'Strategi kombinasi iklan (produk, toko, dan keyword)',
                'Optimasi harian untuk performa iklan',
                'Report performa mingguan + saran penyesuaian',
                'Riset kompetitor & tren pencarian',
                'Waktu Pengelolaan 30 Hari'
            ], 
            'is_popular' => true, 
            'display_order' => 2
        ]);
        
        ServicePackage::create([
            'service_id' => $iklanMarketplace->id, 
            'name' => 'Paket Premium', 
            'price' => 4000000, 
            'duration' => 30, 
            'features' => [
                'Market Ads',
                'Manajemen iklan hingga 10 produk',
                'Diskusi tujuan iklan',
                'Penentuan budget',
                'Penyesuaian strategi berdasarkan tren dan data real-time',
                'A/B Testing iklan untuk hasil optimal',
                'Optimasi harian',
                'Laporan performa mingguan & bulanan lengkap dengan grafik',
                'Konsultasi strategi iklan langsung via Zoom/Gmeet',
                'Waktu Pengelolaan 30 Hari'
            ], 
            'display_order' => 3
        ]);

        // 2. Jasa Optimasi Marketplace
        $optimasiMarketplace = Service::create([
            'category_id' => $marketplace->id,
            'name' => 'Jasa Optimasi Marketplace',
            'slug' => 'optimasi-marketplace',
            'description' => 'Creativemu akan Mengoptimasi MarketPlace anda. Tingkatkan penjualan bisnis Anda menggunakan Jasa Optimasi Marketplace. Anda tidak perlu repot, Creativemu akan melakukannya untuk Anda.',
            'base_price' => 1500000,
            'features' => ['SEO produk', 'Optimasi foto', 'Strategi pricing'],
            'icon' => 'chart-line',
            'is_active' => true,
            'display_order' => 2,
        ]);

        ServicePackage::create([
            'service_id' => $optimasiMarketplace->id, 
            'name' => 'Paket Basic', 
            'price' => 1500000, 
            'duration' => 30, 
            'features' => [
                '1 Akun Marketplace',
                'Optimasi 10 listing produk (Judul, Deskripsi, dan Keyword)',
                'Desain banner toko standar (2x revisi)',
                'Setting etalase dan kategori produk',
                'Riset kata kunci dasar',
                'Waktu pengelolaan 30 Hari',
                'Setting Auto Reply Chat',
                'Laporan performa toko'
            ], 
            'display_order' => 1
        ]);
        
        ServicePackage::create([
            'service_id' => $optimasiMarketplace->id, 
            'name' => 'Paket Pro', 
            'price' => 2500000, 
            'duration' => 30, 
            'features' => [
                '1 Akun Marketplace',
                'Optimasi 30 listing produk (Judul, Deskripsi, dan Keyword)',
                'Manajemen toko harian (balas chat, update stok, kelola pesanan) selama 1 bulan',
                'Desain banner dan elemen visual toko (2x revisi)',
                'Riset dan penempatan keyword untuk SEO marketplace',
                'Setup voucher & promosi toko',
                'Monitoring performa Marketplace',
                'Waktu pengelolaan 30 Hari',
                'Setting Auto Reply Chat',
                'Laporan mingguan',
                'Rekomendasi Strategi'
            ], 
            'is_popular' => true, 
            'display_order' => 2
        ]);
        
        ServicePackage::create([
            'service_id' => $optimasiMarketplace->id, 
            'name' => 'Paket Premium', 
            'price' => 3300000, 
            'duration' => 30, 
            'features' => [
                '1 Akun Marketplace',
                'Optimasi hingga 50 produk (termasuk upload, duplikasi varian, setting category)',
                'Manajemen toko harian (balas chat, update stok, kelola pesanan) selama 1 bulan',
                'Mengelola Affiliate toko',
                'Desain banner toko dan update visual berkala (3x revisi)',
                'Strategi promosi, flash sale, dan campaign marketplace',
                'Riset kompetitor dan keyword untuk SEO Marketplace',
                'Waktu pengelolaan 30 Hari',
                'Setting Auto Reply Chat',
                'Laporan performa mingguan + monthly review'
            ], 
            'display_order' => 3
        ]);

        // 3. Jasa Upload Produk Marketplace
        $uploadProduk = Service::create([
            'category_id' => $marketplace->id,
            'name' => 'Jasa Upload Produk Marketplace',
            'slug' => 'upload-produk',
            'description' => 'Market yang powerfull dimulai dari pengelolaan yang baik. Creativemu menyediakan layanan untuk mengupload produk anda.',
            'base_price' => 150000,
            'features' => ['Bulk upload', 'Edit produk', 'Manajemen stok'],
            'icon' => 'upload',
            'is_active' => true,
            'display_order' => 3,
        ]);

        ServicePackage::create([
            'service_id' => $uploadProduk->id, 
            'name' => 'Paket Ekonomis', 
            'price' => 150000, 
            'duration' => 1, 
            'features' => [
                'Upload Produk 50+10 sku',
                '1 Marketplace (Tokopedia/Shopee)',
                'Konsumen menyediakan data produk (foto, judul, deskripsi, harga, dan keterangan lainnya)',
                'Waktu Pengerjaan 1 hari',
                'Revisi max 4x',
                'Hasil akhir Screenshot hasil upload dan excel upload'
            ], 
            'display_order' => 1
        ]);
        
        ServicePackage::create([
            'service_id' => $uploadProduk->id, 
            'name' => 'Paket Bisnis', 
            'price' => 275000, 
            'duration' => 2, 
            'features' => [
                'Upload Produk 100+10 sku',
                '1 Marketplace (Tokopedia/Shopee)',
                'Konsumen menyediakan data produk (foto, judul, deskripsi, harga, dan keterangan lainnya)',
                'Waktu Pengerjaan 1-2 hari',
                'Revisi max 6x',
                'Hasil akhir Screenshot hasil upload dan excel upload'
            ], 
            'is_popular' => true, 
            'display_order' => 2
        ]);
        
        ServicePackage::create([
            'service_id' => $uploadProduk->id, 
            'name' => 'Paket Executive', 
            'price' => 400000, 
            'duration' => 3, 
            'features' => [
                'Upload Produk 100~200 sku',
                '2 Marketplace (Tokopedia/Shopee)',
                'Konsumen menyediakan data produk (foto, judul, deskripsi, harga, dan keterangan lainnya)',
                'Waktu Pengerjaan 1-3 hari',
                'Revisi max 8x',
                'Hasil akhir Screenshot hasil upload dan excel upload'
            ], 
            'display_order' => 3
        ]);

        // B. Sosial Media
        $sosmed = ServiceCategory::create([
            'name' => 'Sosial Media',
            'slug' => 'sosial-media',
            'description' => 'Layanan manajemen dan advertising sosial media',
            'icon' => 'share-2',
            'is_active' => true,
            'display_order' => 2,
        ]);

        // 4. Jasa Kelola Media Sosial
        $kelolaMediaSosial = Service::create([
            'category_id' => $sosmed->id,
            'name' => 'Jasa Kelola Media Sosial',
            'slug' => 'kelola-sosial-media',
            'description' => 'Creativemu akan Mengelola Media Sosial Anda. Tingkatkan penjualan bisnis Anda menggunakan Jasa Pengelolaan Media Sosial. Anda tidak perlu repot, Creativemu akan melakukannya untuk Anda.',
            'base_price' => 899000,
            'features' => ['Content planning', 'Posting schedule', 'Community management'],
            'icon' => 'users',
            'is_active' => true,
            'display_order' => 1,
        ]);

        ServicePackage::create([
            'service_id' => $kelolaMediaSosial->id,
            'name' => 'Paket Basic',
            'price' => 899000,
            'duration' => 30,
            'features' => [
                '20 Design feed (3 Carousel) + Mirroring Story + 4 Reels Video',
                'General brief dari klien',
                'Semua bahan dari klien atau tidak termasuk take foto dan video',
                'Referensi desain dari klien untuk memahami ekspetasi yang diinginkan',
                'Calender Posting',
                'Caption yang menarik',
                'Revisi Minor'
            ],
            'display_order' => 1
        ]);

        ServicePackage::create([
            'service_id' => $kelolaMediaSosial->id,
            'name' => 'Paket Plus',
            'price' => 1850000,
            'duration' => 30,
            'features' => [
                '25 Design feed (6 Carousel) + Mirroring Story + 4 Reels Video',
                'General brief dari klien',
                'Semua bahan dari klien atau tidak termasuk take foto dan video',
                'Referensi desain dari klien untuk memahami ekspetasi yang diinginkan',
                'Content Planning tertarget',
                'Calender Posting',
                'Admin Posting dan Optimasi',
                'Copywriting & CTA yang kuat',
                'Hashtag & Riset Trend',
                'Revisi minor',
                'WhatsApp Group Team'
            ],
            'is_popular' => true,
            'display_order' => 2
        ]);

        ServicePackage::create([
            'service_id' => $kelolaMediaSosial->id,
            'name' => 'Paket Advance',
            'price' => 2900000,
            'duration' => 30,
            'features' => [
                '30 Design feed (10 Carousel) + Mirroring Story + 6 Reels Video',
                'General brief dari klien',
                'Semua bahan dari klien atau tidak termasuk take foto dan video',
                'Referensi desain dari klien untuk memahami ekspetasi yang diinginkan',
                'Admin Posting dan Optimasi',
                'Content Planning tertarget',
                'Calender Posting',
                'Copywriting storytelling + CTA yang powerful',
                'Free! Meta Ads Rp200.000',
                'Laporan performa mingguan + konsultasi strategi',
                'Laporan Bulanan',
                'WhatsApp Group Team'
            ],
            'display_order' => 3
        ]);

        ServicePackage::create([
            'service_id' => $kelolaMediaSosial->id,
            'name' => 'Take Foto & Video',
            'price' => 500000,
            'duration' => 1,
            'features' => [
                'Durasi Pengambilan kurang lebih 4 Jam'
            ],
            'display_order' => 4
        ]);

        // 5. Jasa Meta Ads
        $metaAds = Service::create([
            'category_id' => $sosmed->id,
            'name' => 'Jasa Meta Ads',
            'slug' => 'jasa-meta-ads',
            'description' => 'Jasa Meta Ads Memudahkan Orang Menemukan Iklan anda. Tingkatkan Iklan anda mengunakan jasa Meta ads agar banyak yang mengetahui tentang iklan anda dan meningkatkan penjualan bisnis anda, Bersama Creativemu.',
            'base_price' => 0,
            'features' => ['Campaign setup', 'Audience targeting', 'A/B testing', 'Report & optimization'],
            'icon' => 'bullhorn',
            'is_active' => true,
            'display_order' => 2,
        ]);

        ServicePackage::create([
            'service_id' => $metaAds->id,
            'name' => 'Paket Basic Meta Ads',
            'price' => 0,
            'duration' => 30,
            'features' => [
                '1 Campaign aktif',
                '2 Ad sets (targeting berbeda)',
                'Riset target audience dasar',
                'Budget plan, Anggaran iklan dari klien',
                'Setting akun iklan',
                'Report bulanan performa iklan',
                'Evaluasi',
                'Pengawalan Daily'
            ],
            'display_order' => 1
        ]);

        ServicePackage::create([
            'service_id' => $metaAds->id,
            'name' => 'Paket Growth Meta Ads',
            'price' => 0,
            'duration' => 30,
            'features' => [
                '2-3 Campaign aktif',
                'Split testing audience & creative',
                'Riset target audience lebih detail',
                'Optimasi mingguan',
                'Budget plan, Anggaran iklan dari klien',
                'Setting akun iklan',
                'Optimasi mingguan',
                'Report mingguan + monthly insights',
                'Evaluasi',
                'Pengawalan Daily'
            ],
            'is_popular' => true,
            'display_order' => 2
        ]);

        ServicePackage::create([
            'service_id' => $metaAds->id,
            'name' => 'Paket Scale Meta Ads',
            'price' => 0,
            'duration' => 30,
            'features' => [
                '4 Campaign aktif',
                'Full funnel strategy (Awareness – Consideration – Conversion)',
                '8 variasi konten iklan (termasuk video reels)',
                'Custom audience & retargeting',
                'A/B Testing rutin',
                'Optimasi harian',
                'Laporan performa & strategi setiap minggu',
                'Konsultasi strategi iklan langsung via Zoom/Gmeet',
                'Anggaran iklan dari klien'
            ],
            'display_order' => 3
        ]);

        // C. Website
        $website = ServiceCategory::create([
            'name' => 'Website',
            'slug' => 'website',
            'description' => 'Layanan pembuatan, SEO, dan manajemen website',
            'icon' => 'globe',
            'is_active' => true,
            'display_order' => 3,
        ]);

        $seoWebsite = Service::create([
            'category_id' => $website->id,
            'name' => 'Jasa SEO Murah Website',
            'slug' => 'seo-website',
            'description' => 'Jasa SEO Website Memungkinkan Website Anda Ditemukan dengan Mudah. Dengan Jasa SEO Website, bisnis Anda akan ditemukan di halaman pertama Google. Hal tersebut akan meningkatkan kunjungan dan meningkatkan penjualan.',
            'base_price' => 5000000,
            'features' => ['Technical SEO', 'On-page optimization', 'Link building', 'Monthly report'],
            'icon' => 'search',
            'is_active' => true,
            'display_order' => 1,
        ]);

        // SEO Organic Packages
        ServicePackage::create([
            'service_id' => $seoWebsite->id,
            'name' => 'Paket Ekonomis',
            'price' => 1500000,
            'duration' => 30,
            'features' => [
                '2 Keyword Utama',
                '3 Keyword Turunan',
                '5 HQ Artikel Per Bulan',
                '5 Backlink per Bulan'
            ],
            'display_order' => 1
        ]);

        ServicePackage::create([
            'service_id' => $seoWebsite->id,
            'name' => 'Paket Bisnis',
            'price' => 2500000,
            'duration' => 30,
            'features' => [
                '3 Keyword Utama',
                '8 Keyword Turunan',
                '20 HQ Artikel Per Bulan',
                '10 Backlink per Bulan'
            ],
            'is_popular' => true,
            'display_order' => 2
        ]);

        ServicePackage::create([
            'service_id' => $seoWebsite->id,
            'name' => 'Paket Corporate',
            'price' => 4900000,
            'duration' => 30,
            'features' => [
                '5 Keyword Utama',
                '20 Keyword Turunan',
                '40 HQ Artikel Per Bulan',
                '20 Backlink per Bulan'
            ],
            'display_order' => 3
        ]);

        // Google Ads Packages
        ServicePackage::create([
            'service_id' => $seoWebsite->id,
            'name' => 'Google Ads Lite',
            'price' => 1000000,
            'duration' => 30,
            'features' => [
                'Riset Kata Kunci',
                'Kelola Google Analytics',
                '5 Kata Kunci',
                'Web Performance',
                'Durasi 30 hari',
                'Laporan Akhir',
                '** Belum termasuk dana iklan Google Ads'
            ],
            'display_order' => 4
        ]);

        ServicePackage::create([
            'service_id' => $seoWebsite->id,
            'name' => 'Google Ads Medium',
            'price' => 1500000,
            'duration' => 30,
            'features' => [
                'Riset Kata Kunci',
                'Kelola Google Analytics',
                '10 Kata Kunci',
                'Web Performance',
                'Durasi 30 hari',
                'Laporan Akhir',
                'Bonus 5 Backlink',
                '** Belum termasuk dana iklan Google Ads'
            ],
            'display_order' => 5
        ]);

        ServicePackage::create([
            'service_id' => $seoWebsite->id,
            'name' => 'Google Ads Large',
            'price' => 2200000,
            'duration' => 30,
            'features' => [
                'Riset Kata Kunci',
                'Kelola Google Analytics',
                'Unlimited Kata Kunci',
                'Web Performance',
                'Durasi 30 hari',
                'Laporan Akhir',
                'Bonus 10 Backlink',
                '** Belum termasuk dana iklan Google Ads'
            ],
            'display_order' => 6
        ]);

        $pembuatanWebsite = Service::create([
            'category_id' => $website->id,
            'name' => 'Jasa Pembuatan Website Berkualitas',
            'slug' => 'pembuatan-website',
            'description' => 'Creativemu akan Bantu Kamu untuk Membuat Website Berkualitas. Jasa Pembuatan Website Profesional untuk Personal Blog, Company Profile, Website Portofolio, Landing page bagi pelaku usaha UMKM atau perusahaan dengan layanan berkualitas dan harga yang murah.',
            'base_price' => 8000000,
            'features' => ['Custom design', 'Responsive', 'CMS', 'Free maintenance 3 bulan'],
            'icon' => 'laptop-code',
            'is_active' => true,
            'display_order' => 2,
        ]);

        ServicePackage::create([
            'service_id' => $pembuatanWebsite->id,
            'name' => 'Landing Page',
            'price' => 1200000,
            'duration' => 14,
            'features' => [
                'Gratis domain ".com"',
                '500MB Storage',
                'Custom 1 Halaman',
                'Optimasi Pagespeed',
                'Optimasi Keamanan',
                'Sertifikat SSL/HTTPS',
                'Revisi Desain 1x',
                'Waktu Pengerjaan 1-2 Minggu',
                '*Syarat dan Ketentuan Berlaku'
            ],
            'display_order' => 1
        ]);

        ServicePackage::create([
            'service_id' => $pembuatanWebsite->id,
            'name' => 'Company Profile',
            'price' => 2700000,
            'duration' => 21,
            'features' => [
                'Gratis domain ".com"',
                '4GB Storage',
                'Custom 7 Halaman',
                'Optimasi Pagespeed',
                'Optimasi Keamanan',
                'Sertifikat SSL/HTTPS',
                'Revisi Desain 2x',
                'SEO Friendly',
                'Waktu Pengerjaan 2-3 Minggu',
                '*Syarat dan Ketentuan Berlaku'
            ],
            'is_popular' => true,
            'display_order' => 2
        ]);

        $kelolaWebsite = Service::create([
            'category_id' => $website->id,
            'name' => 'Jasa Kelola Website',
            'slug' => 'kelola-website',
            'description' => 'Website yang powerful dimulai dari pengelolaan yang baik. Creativemu menyediakan layanan untuk mengelola website Anda. Seperti tumbuhan, jika website tidak dirawat, nanti akan mati dengan sendirinya.',
            'base_price' => 2000000,
            'features' => ['Content update', 'Security monitoring', 'Backup', 'Technical support'],
            'icon' => 'cog',
            'is_active' => true,
            'display_order' => 3,
        ]);

        ServicePackage::create([
            'service_id' => $kelolaWebsite->id,
            'name' => 'Paket Lite',
            'price' => 750000,
            'duration' => 7,
            'features' => [
                'Optimasi Kecepatan',
                'Optimasi Keamanan',
                'Update Plugin',
                'Ganti Tema',
                '1 Halaman Update',
                'Backup Website 1x',
                'Waktu Pengerjaan 1 Minggu',
                '*Syarat dan Ketentuan Berlaku'
            ],
            'display_order' => 1
        ]);

        ServicePackage::create([
            'service_id' => $kelolaWebsite->id,
            'name' => 'Paket Medium',
            'price' => 1200000,
            'duration' => 14,
            'features' => [
                'Optimasi Kecepatan',
                'Optimasi Keamanan',
                'Update Plugin',
                'Ganti Tema',
                '3 Halaman Update',
                'Backup Website 2x',
                'Waktu Pengerjaan 2 Minggu',
                '*Syarat dan Ketentuan Berlaku'
            ],
            'is_popular' => true,
            'display_order' => 2
        ]);

        ServicePackage::create([
            'service_id' => $kelolaWebsite->id,
            'name' => 'Paket Full',
            'price' => 2000000,
            'duration' => 30,
            'features' => [
                'Optimasi Kecepatan',
                'Optimasi Keamanan',
                'Update Plugin',
                'Ganti Tema',
                'Unlimited Halaman Update',
                'Backup Website 3x',
                'Waktu Pengerjaan 1 Bulan',
                '*Syarat dan Ketentuan Berlaku'
            ],
            'display_order' => 3
        ]);

        // D. Academy
        $academy = ServiceCategory::create([
            'name' => 'Academy',
            'slug' => 'academy',
            'description' => 'Pelatihan dan sertifikasi digital marketing',
            'icon' => 'award',
            'is_active' => true,
            'display_order' => 4,
        ]);

        $pelatihanDigitalMarketing = Service::create([
            'category_id' => $academy->id,
            'name' => 'Tingkatkan Dirimu dengan Digital Marketing Profesional',
            'slug' => 'pelatihan-digital-marketing',
            'description' => 'Tambah skil kamu di bidang Digital Marketing, ciptakan dan naikkan omset, hingga dicari-cari oleh banyak perusahaan. Pilih bootcamp dan kelas yang kamu rasa cocok.',
            'base_price' => 5000000,
            'features' => ['Materi lengkap', 'Praktik langsung', 'Sertifikat', 'Mentoring'],
            'icon' => 'graduation-cap',
            'is_active' => true,
            'display_order' => 1,
        ]);

        ServicePackage::create([
            'service_id' => $pelatihanDigitalMarketing->id,
            'name' => 'Pelatihan Digital Marketing',
            'price' => 1500000,
            'duration' => 30,
            'features' => [
                '5x Pertemuan',
                'Level Pemula',
                'Sertifikat',
                'Tersedia Online & Offline',
                'Tersedia Kelas Privat'
            ],
            'is_popular' => true,
            'display_order' => 1
        ]);

        ServicePackage::create([
            'service_id' => $pelatihanDigitalMarketing->id,
            'name' => 'Social Media Marketing',
            'price' => 1500000,
            'duration' => 30,
            'features' => [
                '5x Pertemuan',
                'Level Pemula',
                'Sertifikat',
                'Tersedia Online & Offline',
                'Tersedia Kelas Privat'
            ],
            'display_order' => 2
        ]);

        ServicePackage::create([
            'service_id' => $pelatihanDigitalMarketing->id,
            'name' => 'Admin Marketplace (Basic)',
            'price' => 1200000,
            'duration' => 30,
            'features' => [
                '4x Pertemuan',
                'Level Pemula',
                'Sertifikat',
                'Tersedia Online & Offline',
                'Tersedia Kelas Privat'
            ],
            'display_order' => 3
        ]);

        ServicePackage::create([
            'service_id' => $pelatihanDigitalMarketing->id,
            'name' => 'Meta Ads FB & IG',
            'price' => 1890000,
            'duration' => 30,
            'features' => [
                '3x Pertemuan',
                'Level Pemula',
                'Sertifikat',
                'Tersedia Online & Offline',
                'Tersedia Kelas Privat'
            ],
            'display_order' => 4
        ]);

        ServicePackage::create([
            'service_id' => $pelatihanDigitalMarketing->id,
            'name' => 'Menguasai Sosial Media Marketing untuk Periklanan dan Pemasaran',
            'price' => 1500000,
            'duration' => 30,
            'features' => [
                '5x Pertemuan',
                'Level Pemula',
                'Sertifikat',
                'Kelas Online'
            ],
            'display_order' => 5
        ]);

        Service::create([
            'category_id' => $academy->id,
            'name' => 'Magang dan Prakerin SMK',
            'slug' => 'magang-prakerin',
            'description' => 'Tingkatkan Pengalaman Kerja dengan Magang dan Prakerin. Magang dan prakerin sangat penting untuk peningkatan pengalaman di dunia kerja. Magang dan prakerin juga dapat menjadi ajang mencari relasi. Daftar Magang/Prakerin di Creativemu Sekarang Juga!',
            'base_price' => 0,
            'features' => ['Real project', 'Mentoring', 'Sertifikat', 'Portfolio', 'Gratis - Tidak ada biaya'],
            'icon' => 'user-graduate',
            'is_active' => true,
            'display_order' => 2,
        ]);

        Service::create([
            'category_id' => $academy->id,
            'name' => 'Sertifikasi BNSP',
            'slug' => 'sertifikasi-bnsp',
            'description' => 'Buktikan Kompetensi Anda dengan Sertifikasi Digital Marketing. Creativemu Academy merupakan tempat uji kompetensi (TUK) didalam LSP Digital. Terdapat banyak skema sertifikasi yang bisa Anda ikuti. Ayo Daftar Sertifikasi LSP di Creativemu Academy Sekarang Juga!',
            'base_price' => 0,
            'features' => ['Training', 'Uji kompetensi', 'Sertifikat BNSP', 'Garansi lulus', 'Hubungi untuk informasi harga'],
            'icon' => 'certificate',
            'is_active' => true,
            'display_order' => 3,
        ]);
    }
}

