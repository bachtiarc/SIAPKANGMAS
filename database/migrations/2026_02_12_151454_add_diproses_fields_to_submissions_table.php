<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('submissions', 'diproses_bidang')) {
                $table->string('diproses_bidang', 255)->nullable();
            }
            if (!Schema::hasColumn('submissions', 'diproses_kelompok')) {
                $table->string('diproses_kelompok', 255)->nullable();
            }
            if (!Schema::hasColumn('submissions', 'diproses_oleh')) {
                $table->string('diproses_oleh', 255)->nullable(); // gabungan "Bidang - Kelompok"
            }
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (Schema::hasColumn('submissions', 'diproses_bidang')) {
                $table->dropColumn('diproses_bidang');
            }
            if (Schema::hasColumn('submissions', 'diproses_kelompok')) {
                $table->dropColumn('diproses_kelompok');
            }
            if (Schema::hasColumn('submissions', 'diproses_oleh')) {
                $table->dropColumn('diproses_oleh');
            }
        });
    }
};