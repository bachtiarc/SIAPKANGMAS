<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PegawaiCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $categories = [
            // ==================== PERMOHONAN INFORMASI ====================
            [
                'name' => 'Data Kepegawaian',
                'type' => 'permohonan',
                'user_type' => 'pegawai',
                'description' => 'Permohonan data kepegawaian, riwayat karir, dan informasi terkait SDM internal',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Data Anggaran dan Keuangan',
                'type' => 'permohonan',
                'user_type' => 'pegawai',
                'description' => 'Permohonan informasi anggaran, realisasi, dan laporan keuangan internal',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Data Program dan Kegiatan',
                'type' => 'permohonan',
                'user_type' => 'pegawai',
                'description' => 'Informasi program, kegiatan, dan capaian kinerja antar bidang/balai',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Dokumen Kebijakan Internal',
                'type' => 'permohonan',
                'user_type' => 'pegawai',
                'description' => 'Permohonan akses ke SK, SOP, Juknis, dan dokumen kebijakan internal',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Data Aset dan Inventaris',
                'type' => 'permohonan',
                'user_type' => 'pegawai',
                'description' => 'Informasi aset, inventaris, dan sarana prasarana dinas',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Laporan dan Statistik Internal',
                'type' => 'permohonan',
                'user_type' => 'pegawai',
                'description' => 'Laporan kinerja, statistik, dan data monitoring internal',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Koordinasi Antar Bidang/Balai',
                'type' => 'permohonan',
                'user_type' => 'pegawai',
                'description' => 'Permohonan informasi untuk koordinasi antar unit kerja',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Database dan Sistem Informasi',
                'type' => 'permohonan',
                'user_type' => 'pegawai',
                'description' => 'Akses database, sistem informasi, dan aplikasi internal',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ==================== KONSULTASI ====================
            [
                'name' => 'Konsultasi Teknis Pekerjaan',
                'type' => 'konsultasi',
                'user_type' => 'pegawai',
                'description' => 'Konsultasi terkait teknis pelaksanaan pekerjaan dan tugas',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Konsultasi Administrasi',
                'type' => 'konsultasi',
                'user_type' => 'pegawai',
                'description' => 'Konsultasi administrasi, persuratan, dan tata kelola',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Konsultasi Keuangan dan Anggaran',
                'type' => 'konsultasi',
                'user_type' => 'pegawai',
                'description' => 'Konsultasi terkait penggunaan anggaran dan pelaporan keuangan',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Konsultasi Kepegawaian',
                'type' => 'konsultasi',
                'user_type' => 'pegawai',
                'description' => 'Konsultasi terkait karir, mutasi, dan pengembangan SDM',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ==================== PENGADUAN ====================
            [
                'name' => 'Pengaduan Fasilitas Kerja',
                'type' => 'pengaduan',
                'user_type' => 'pegawai',
                'description' => 'Pengaduan terkait fasilitas kerja, sarana prasarana kantor',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Pengaduan Layanan Internal',
                'type' => 'pengaduan',
                'user_type' => 'pegawai',
                'description' => 'Pengaduan terkait pelayanan antar unit kerja',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Pengaduan Sistem dan Aplikasi',
                'type' => 'pengaduan',
                'user_type' => 'pegawai',
                'description' => 'Pengaduan terkait sistem informasi dan aplikasi internal',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Pengaduan Lingkungan Kerja',
                'type' => 'pengaduan',
                'user_type' => 'pegawai',
                'description' => 'Pengaduan terkait lingkungan kerja dan kondisi kantor',
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

        $this->command->info('✓ Kategori untuk PEGAWAI berhasil dibuat!');
        $this->command->info('  - 8 kategori Permohonan Informasi');
        $this->command->info('  - 4 kategori Konsultasi');
        $this->command->info('  - 4 kategori Pengaduan');
    }
}