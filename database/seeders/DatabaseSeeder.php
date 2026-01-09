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
        // Create default admin user
        User::create([
            'name' => 'Admin SIAPKANGMAS',
            'nip' => '199000000000000001',
            'email' => 'admin@disperindag.jatengprov.go.id',
            'phone' => '081234567890',
            'role' => 'admin',
            'bidang' => 'IT & Sistem Informasi',
            'jabatan' => 'Administrator Sistem',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'), // GANTI PASSWORD INI di production!
        ]);

        // Default categories untuk Permohonan Informasi
        $permohonanCategories = [
            'Informasi Perizinan Usaha',
            'Informasi Ekspor Produk',
            'Informasi Impor Barang',
            'Sertifikasi Produk',
            'Standar Nasional Indonesia (SNI)',
            'Pendaftaran Merek Dagang',
        ];

        foreach ($permohonanCategories as $category) {
            Category::create([
                'name' => $category,
                'type' => 'permohonan',
                'description' => 'Kategori untuk permohonan ' . $category,
                'is_active' => true,
            ]);
        }

        // Default categories untuk Konsultasi
        $konsultasiCategories = [
            'Konsultasi Ekspor/Impor',
            'Konsultasi Sektor Industri Tekstil',
            'Konsultasi Sektor Industri Makanan & Minuman',
            'Konsultasi Sektor Industri Kimia',
            'Konsultasi Sektor Industri Logam',
            'Konsultasi Pengembangan UKM',
        ];

        foreach ($konsultasiCategories as $category) {
            Category::create([
                'name' => $category,
                'type' => 'konsultasi',
                'description' => 'Kategori untuk ' . $category,
                'is_active' => true,
            ]);
        }

        // Default categories untuk Pengaduan
        $pengaduanCategories = [
            'Keluhan Layanan',
            'Pelanggaran Standar Produk',
            'Produk Tidak Sesuai SNI',
            'Perizinan Bermasalah',
            'Lainnya',
        ];

        foreach ($pengaduanCategories as $category) {
            Category::create([
                'name' => $category,
                'type' => 'pengaduan',
                'description' => 'Kategori untuk pengaduan ' . $category,
                'is_active' => true,
            ]);
        }
    }
}