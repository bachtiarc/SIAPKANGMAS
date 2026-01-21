<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class ComplaintCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $complaintCategories = [
            [
                'name' => 'Pelayanan Administrasi',
                'description' => 'Pengaduan terkait pelayanan administrasi seperti pengurusan surat, dokumen, dan layanan administrasi lainnya',
                'type' => 'pengaduan',
                'is_active' => true,
            ],
            [
                'name' => 'Fasilitas Kantor',
                'description' => 'Pengaduan terkait fasilitas kantor seperti AC, listrik, toilet, ruang kerja, dan sarana prasarana lainnya',
                'type' => 'pengaduan',
                'is_active' => true,
            ],
            [
                'name' => 'Sistem Informasi',
                'description' => 'Pengaduan terkait sistem informasi, aplikasi, website, dan teknologi informasi',
                'type' => 'pengaduan',
                'is_active' => true,
            ],
            [
                'name' => 'Koordinasi Antar Bidang',
                'description' => 'Pengaduan terkait koordinasi dan komunikasi antar bidang atau unit kerja',
                'type' => 'pengaduan',
                'is_active' => true,
            ],
            [
                'name' => 'Kepegawaian',
                'description' => 'Pengaduan terkait masalah kepegawaian seperti gaji, tunjangan, promosi, dan kesejahteraan pegawai',
                'type' => 'pengaduan',
                'is_active' => true,
            ],
            [
                'name' => 'Pengadaan Barang/Jasa',
                'description' => 'Pengaduan terkait proses pengadaan barang dan jasa',
                'type' => 'pengaduan',
                'is_active' => true,
            ],
            [
                'name' => 'Etika dan Disiplin',
                'description' => 'Pengaduan terkait pelanggaran etika, disiplin, dan tata tertib pegawai',
                'type' => 'pengaduan',
                'is_active' => true,
            ],
            [
                'name' => 'Lingkungan Kerja',
                'description' => 'Pengaduan terkait kondisi lingkungan kerja, kebersihan, keamanan, dan kenyamanan',
                'type' => 'pengaduan',
                'is_active' => true,
            ],
            [
                'name' => 'Keuangan',
                'description' => 'Pengaduan terkait masalah keuangan, anggaran, dan pembayaran',
                'type' => 'pengaduan',
                'is_active' => true,
            ],
            [
                'name' => 'Lainnya',
                'description' => 'Pengaduan lainnya yang tidak termasuk dalam kategori yang tersedia',
                'type' => 'pengaduan',
                'is_active' => true,
            ],
        ];

        foreach ($complaintCategories as $category) {
            // Cek apakah kategori sudah ada berdasarkan name dan type
            $existing = Category::where('name', $category['name'])
                ->where('type', 'pengaduan')
                ->first();
            
            if (!$existing) {
                // Kalau belum ada, create baru
                Category::create($category);
                $this->command->info("✅ Created: {$category['name']}");
            } else {
                // Kalau sudah ada, update
                $existing->update($category);
                $this->command->info("♻️  Updated: {$category['name']}");
            }
        }

        $this->command->info('');
        $this->command->info('🎉 Complaint categories seeded successfully!');
        $this->command->info("📊 Total categories: " . count($complaintCategories));
    }
}