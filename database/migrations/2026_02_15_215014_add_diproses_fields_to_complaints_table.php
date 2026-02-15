<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->string('diproses_bidang')->nullable()->after('handled_by');
            $table->string('diproses_kelompok')->nullable()->after('diproses_bidang');
            $table->string('diproses_oleh')->nullable()->after('diproses_kelompok');
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['diproses_bidang', 'diproses_kelompok', 'diproses_oleh']);
        });
    }
};