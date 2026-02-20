<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ========================================
        // SEED DATA WILAYAH (PROV/KAB/KEC/DESA)
        // ========================================
        $this->call(\Database\Seeders\RegionalSeeder::class);

        // ========================================
        // CREATE OR UPDATE ADMIN USER
        // ========================================
        $adminEmail = 'admin@disperindag.jatengprov.go.id';

        $admin = User::where('email', $adminEmail)->first();

        if (!$admin) {
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
            $admin->update([
                'role' => 'admin',
                'user_type' => $admin->user_type ?: 'pegawai',
                'email_verified_at' => $admin->email_verified_at ?? now(),
            ]);
            $this->command->info('ℹ️  Admin user already exists, updated!');
        }

        // ========================================
        // CREATE OR UPDATE CO ADMIN USER (LOGIN VIA TAB ADMIN)
        // ========================================
        $coAdminEmail = 'adminsiapkangmas@gmail.com';

        $coAdmin = User::where('email', $coAdminEmail)->first();

        if (!$coAdmin) {
            User::create([
                'name' => 'CO ADMIN SIAPKANGMAS',
                'email' => $coAdminEmail,
                'phone' => null,
                'role' => 'admin',
                'user_type' => 'pegawai',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
            ]);

            $this->command->info('✅ CO ADMIN user created!');
        } else {
            $coAdmin->update([
                'role' => 'admin',
                'user_type' => $coAdmin->user_type ?? 'pegawai',
                'email_verified_at' => $coAdmin->email_verified_at ?? now(),
                'password' => Hash::make('12345678'),
            ]);

            $this->command->info('✅ CO ADMIN user updated!');
        }

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('👤 Admin: ' . $adminEmail);
        $this->command->info('👤 CO ADMIN: ' . $coAdminEmail);
        $this->command->info('========================================');
    }
}