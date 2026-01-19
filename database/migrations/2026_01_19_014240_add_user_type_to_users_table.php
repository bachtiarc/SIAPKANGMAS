<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah user_type jika belum ada
            if (!Schema::hasColumn('users', 'user_type')) {
                $table->enum('user_type', ['pegawai', 'masyarakat_umum'])
                    ->default('masyarakat_umum')
                    ->after('role');
            }
            
            $table->string('nip', 18)->nullable()->change();
            $table->string('bidang')->nullable()->change();
            $table->string('jabatan')->nullable()->change();
            
            if (!Schema::hasColumn('users', 'nik')) {
                $table->string('nik', 16)->nullable()->after('nip');
            }
            
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('users', 'foto_ktp')) {
                $table->string('foto_ktp')->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['user_type', 'nik', 'address', 'foto_ktp']);
        });
    }
};