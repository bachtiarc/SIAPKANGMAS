<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasyarakatCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $categories = [
            // ==================== PERMOHONAN INFORMASI ====================
            [
                'name' => 'Informasi Perizinan Usaha',
                'type' => 'permohonan',
                'user_type' => 'masyarakat_umum',
                'description' => 'Informasi mengenai perizinan usaha, izin industri, dan persyaratan legalitas usaha',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Informasi Standarisasi Produk dan SNI',
                'type' => 'permohonan',
                'user_type' => 'masyarakat_umum',
                'description' => 'Informasi mengenai standar produk, SNI, dan sertifikasi mutu produk',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Informasi Perlindungan Konsumen',
                'type' => 'permohonan',
                'user_type' => 'masyarakat_umum',
                'description' => 'Informasi mengenai hak konsumen, pengaduan konsumen, dan perlindungan konsumen',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Informasi Perdagangan dan Pasar',
                'type' => 'permohonan',
                'user_type' => 'masyarakat_umum',
                'description' => 'Informasi mengenai perdagangan, harga pasar, dan distribusi barang',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Informasi Ekspor dan Impor',
                'type' => 'permohonan',
                'user_type' => 'masyarakat_umum',
                'description' => 'Informasi mengenai prosedur ekspor, impor, dan perdagangan internasional',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Informasi Program UMKM',
                'type' => 'permohonan',
                'user_type' => 'masyarakat_umum',
                'description' => 'Informasi program pembinaan, pelatihan, dan fasilitasi UMKM',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Informasi Pengujian dan Sertifikasi',
                'type' => 'permohonan',
                'user_type' => 'masyarakat_umum',
                'description' => 'Informasi layanan pengujian produk dan sertifikasi mutu barang',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Informasi Layanan Kalibrasi',
                'type' => 'permohonan',
                'user_type' => 'masyarakat_umum',
                'description' => 'Informasi layanan kalibrasi alat ukur dan instrumentasi',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Informasi Pengembangan Industri',
                'type' => 'permohonan',
                'user_type' => 'masyarakat_umum',
                'description' => 'Informasi pengembangan industri, inovasi produk, dan teknologi',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Informasi Data dan Statistik Publik',
                'type' => 'permohonan',
                'user_type' => 'masyarakat_umum',
                'description' => 'Data statistik, laporan, dan analisis perdagangan dan industri yang dapat diakses publik',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ==================== KONSULTASI ====================
            [
                'name' => 'Konsultasi Perizinan Usaha',
                'type' => 'konsultasi',
                'user_type' => 'masyarakat_umum',
                'description' => 'Konsultasi terkait pengurusan perizinan usaha dan industri',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Konsultasi Pengembangan UMKM',
                'type' => 'konsultasi',
                'user_type' => 'masyarakat_umum',
                'description' => 'Konsultasi pengembangan usaha mikro, kecil, dan menengah',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Konsultasi Standarisasi Produk',
                'type' => 'konsultasi',
                'user_type' => 'masyarakat_umum',
                'description' => 'Konsultasi terkait standarisasi produk dan sertifikasi',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Konsultasi Ekspor Impor',
                'type' => 'konsultasi',
                'user_type' => 'masyarakat_umum',
                'description' => 'Konsultasi prosedur dan persyaratan ekspor impor',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Konsultasi Perlindungan Konsumen',
                'type' => 'konsultasi',
                'user_type' => 'masyarakat_umum',
                'description' => 'Konsultasi terkait hak konsumen dan penyelesaian sengketa konsumen',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ==================== PENGADUAN ====================
            [
                'name' => 'Pengaduan Produk Tidak Standar',
                'type' => 'pengaduan',
                'user_type' => 'masyarakat_umum',
                'description' => 'Pengaduan terkait produk yang tidak memenuhi standar atau SNI',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Pengaduan Praktik Perdagangan',
                'type' => 'pengaduan',
                'user_type' => 'masyarakat_umum',
                'description' => 'Pengaduan terkait praktik perdagangan yang merugikan konsumen',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Pengaduan Harga dan Ketersediaan Barang',
                'type' => 'pengaduan',
                'user_type' => 'masyarakat_umum',
                'description' => 'Pengaduan terkait kenaikan harga atau kelangkaan barang',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Pengaduan Perizinan Usaha',
                'type' => 'pengaduan',
                'user_type' => 'masyarakat_umum',
                'description' => 'Pengaduan terkait proses atau pelayanan perizinan usaha',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Pengaduan Layanan Pengujian',
                'type' => 'pengaduan',
                'user_type' => 'masyarakat_umum',
                'description' => 'Pengaduan terkait layanan pengujian dan sertifikasi produk',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($categories as $category) {
            // Check if category already exists
            $exists = DB::table('categories')
                ->where('name', $category['name'])
                ->where('type', $category['type'])
                ->where('user_type', $category['user_type'])
                ->exists();

            if (!$exists) {
                DB::table('categories')->insert($category);
            }
        }

        $this->command->info('✓ Kategori untuk MASYARAKAT UMUM berhasil dibuat!');
        $this->command->info('  - 10 kategori Permohonan Informasi');
        $this->command->info('  - 5 kategori Konsultasi');
        $this->command->info('  - 5 kategori Pengaduan');
    }
}