<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            if (!Schema::hasColumn('consultations', 'diproses_bidang')) {
                $table->string('diproses_bidang')->nullable()->after('handled_by');
            }
            if (!Schema::hasColumn('consultations', 'diproses_kelompok')) {
                $table->string('diproses_kelompok')->nullable()->after('diproses_bidang');
            }
            if (!Schema::hasColumn('consultations', 'diproses_oleh')) {
                $table->string('diproses_oleh')->nullable()->after('diproses_kelompok');
            }
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            if (Schema::hasColumn('consultations', 'diproses_oleh')) {
                $table->dropColumn('diproses_oleh');
            }
            if (Schema::hasColumn('consultations', 'diproses_kelompok')) {
                $table->dropColumn('diproses_kelompok');
            }
            if (Schema::hasColumn('consultations', 'diproses_bidang')) {
                $table->dropColumn('diproses_bidang');
            }
        });
    }
};