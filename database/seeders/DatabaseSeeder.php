<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ========================================
        // CREATE OR UPDATE ADMIN USER
        // ========================================
        $adminEmail = 'admin@disperindag.jatengprov.go.id';
        
        $admin = User::where('email', $adminEmail)->first();
        
        if (!$admin) {
            // Create new admin if doesn't exist
            User::create([
                'name' => 'Admin SIAPKANGMAS',
                'nip' => '199000000000000001',
                'email' => $adminEmail,
                'phone' => '628123456789',
                'role' => 'admin',
                'user_type' => 'pegawai',
                'bidang' => 'Sekretariat',
                'jabatan' => 'Sekeretariat',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
            ]);
            $this->command->info('✅ Admin user created!');
        } else {
            // Update existing admin's user_type if needed
            if (!$admin->user_type) {
                $admin->update(['user_type' => 'pegawai']);
                $this->command->info('✅ Admin user updated with user_type!');
            } else {
                $this->command->info('ℹ️  Admin user already exists, skipping...');
            }
        }

        // ========================================
        // PERMOHONAN INFORMASI CATEGORIES
        // ========================================
        $permohonanCategories = [
            // Original categories
            [
                'name' => 'Informasi Perizinan Usaha',
                'type' => 'permohonan',
                'description' => 'Kategori untuk permohonan Informasi Perizinan Usaha',
                'is_active' => true,
            ],
            [
                'name' => 'Informasi Ekspor Produk',
                'type' => 'permohonan',
                'description' => 'Kategori untuk permohonan Informasi Ekspor Produk',
                'is_active' => true,
            ],
            [
                'name' => 'Informasi Impor Barang',
                'type' => 'permohonan',
                'description' => 'Kategori untuk permohonan Informasi Impor Barang',
                'is_active' => true,
            ],
            [
                'name' => 'Sertifikasi Produk',
                'type' => 'permohonan',
                'description' => 'Kategori untuk permohonan Sertifikasi Produk',
                'is_active' => true,
            ],
            [
                'name' => 'Standar Nasional Indonesia (SNI)',
                'type' => 'permohonan',
                'description' => 'Kategori untuk permohonan Standar Nasional Indonesia (SNI)',
                'is_active' => true,
            ],
            [
                'name' => 'Pendaftaran Merek Dagang',
                'type' => 'permohonan',
                'description' => 'Kategori untuk permohonan Pendaftaran Merek Dagang',
                'is_active' => true,
            ],
            
            // NEW categories
            [
                'name' => 'Data Ekspor',
                'type' => 'permohonan',
                'description' => 'Permohonan data statistik ekspor',
                'is_active' => true,
            ],
            [
                'name' => 'Data Impor',
                'type' => 'permohonan',
                'description' => 'Permohonan data statistik impor',
                'is_active' => true,
            ],
            [
                'name' => 'Data Industri',
                'type' => 'permohonan',
                'description' => 'Permohonan data industri di Jawa Tengah',
                'is_active' => true,
            ],
            [
                'name' => 'Regulasi Perdagangan',
                'type' => 'permohonan',
                'description' => 'Permohonan informasi regulasi perdagangan',
                'is_active' => true,
            ],
            [
                'name' => 'Standardisasi Produk',
                'type' => 'permohonan',
                'description' => 'Permohonan informasi standardisasi produk',
                'is_active' => true,
            ],
        ];

        $permohonanCreated = 0;
        foreach ($permohonanCategories as $categoryData) {
            // Check if category already exists
            $exists = Category::where('name', $categoryData['name'])
                ->where('type', $categoryData['type'])
                ->exists();
            
            if (!$exists) {
                Category::create($categoryData);
                $permohonanCreated++;
            }
        }
        $this->command->info("✅ Permohonan categories: $permohonanCreated new, " . (count($permohonanCategories) - $permohonanCreated) . " existing");

        // ========================================
        // KONSULTASI CATEGORIES
        // ========================================
        $konsultasiCategories = [
            // Original categories
            [
                'name' => 'Konsultasi Ekspor/Impor',
                'type' => 'konsultasi',
                'description' => 'Kategori untuk Konsultasi Ekspor/Impor',
                'is_active' => true,
            ],
            [
                'name' => 'Konsultasi Sektor Industri Tekstil',
                'type' => 'konsultasi',
                'description' => 'Kategori untuk Konsultasi Sektor Industri Tekstil',
                'is_active' => true,
            ],
            [
                'name' => 'Konsultasi Sektor Industri Makanan & Minuman',
                'type' => 'konsultasi',
                'description' => 'Kategori untuk Konsultasi Sektor Industri Makanan & Minuman',
                'is_active' => true,
            ],
            [
                'name' => 'Konsultasi Sektor Industri Kimia',
                'type' => 'konsultasi',
                'description' => 'Kategori untuk Konsultasi Sektor Industri Kimia',
                'is_active' => true,
            ],
            [
                'name' => 'Konsultasi Sektor Industri Logam',
                'type' => 'konsultasi',
                'description' => 'Kategori untuk Konsultasi Sektor Industri Logam',
                'is_active' => true,
            ],
            [
                'name' => 'Konsultasi Pengembangan UKM',
                'type' => 'konsultasi',
                'description' => 'Kategori untuk Konsultasi Pengembangan UKM',
                'is_active' => true,
            ],
            
            // NEW categories
            [
                'name' => 'Konsultasi Perizinan Usaha',
                'type' => 'konsultasi',
                'description' => 'Konsultasi perizinan usaha industri dan perdagangan',
                'is_active' => true,
            ],
            [
                'name' => 'Konsultasi Pengembangan Produk',
                'type' => 'konsultasi',
                'description' => 'Konsultasi pengembangan produk industri',
                'is_active' => true,
            ],
        ];

        $konsultasiCreated = 0;
        foreach ($konsultasiCategories as $categoryData) {
            $exists = Category::where('name', $categoryData['name'])
                ->where('type', $categoryData['type'])
                ->exists();
            
            if (!$exists) {
                Category::create($categoryData);
                $konsultasiCreated++;
            }
        }
        $this->command->info("✅ Konsultasi categories: $konsultasiCreated new, " . (count($konsultasiCategories) - $konsultasiCreated) . " existing");

        // ========================================
        // PENGADUAN CATEGORIES
        // ========================================
        $pengaduanCategories = [
            // Original categories
            [
                'name' => 'Keluhan Layanan',
                'type' => 'pengaduan',
                'description' => 'Kategori untuk pengaduan Keluhan Layanan',
                'is_active' => true,
            ],
            [
                'name' => 'Pelanggaran Standar Produk',
                'type' => 'pengaduan',
                'description' => 'Kategori untuk pengaduan Pelanggaran Standar Produk',
                'is_active' => true,
            ],
            [
                'name' => 'Produk Tidak Sesuai SNI',
                'type' => 'pengaduan',
                'description' => 'Kategori untuk pengaduan Produk Tidak Sesuai SNI',
                'is_active' => true,
            ],
            [
                'name' => 'Perizinan Bermasalah',
                'type' => 'pengaduan',
                'description' => 'Kategori untuk pengaduan Perizinan Bermasalah',
                'is_active' => true,
            ],
            [
                'name' => 'Lainnya',
                'type' => 'pengaduan',
                'description' => 'Kategori untuk pengaduan Lainnya',
                'is_active' => true,
            ],
            
            // NEW categories
            [
                'name' => 'Pengaduan Konsumen',
                'type' => 'pengaduan',
                'description' => 'Pengaduan terkait perlindungan konsumen',
                'is_active' => true,
            ],
            [
                'name' => 'Pengaduan Perdagangan',
                'type' => 'pengaduan',
                'description' => 'Pengaduan praktik perdagangan tidak sehat',
                'is_active' => true,
            ],
            [
                'name' => 'Pengaduan Pelayanan',
                'type' => 'pengaduan',
                'description' => 'Pengaduan pelayanan DISPERINDAG',
                'is_active' => true,
            ],
        ];

        $pengaduanCreated = 0;
        foreach ($pengaduanCategories as $categoryData) {
            $exists = Category::where('name', $categoryData['name'])
                ->where('type', $categoryData['type'])
                ->exists();
            
            if (!$exists) {
                Category::create($categoryData);
                $pengaduanCreated++;
            }
        }
        $this->command->info("✅ Pengaduan categories: $pengaduanCreated new, " . (count($pengaduanCategories) - $pengaduanCreated) . " existing");

        // ========================================
        // SUMMARY
        // ========================================
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('📊 Total categories: ' . Category::count());
        $this->command->info('👤 Admin: ' . $adminEmail);
        $this->command->info('========================================');
    }
}