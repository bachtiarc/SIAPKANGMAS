<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('complaint_applicants', function (Blueprint $table) {
            if (!Schema::hasColumn('complaint_applicants', 'pekerjaan')) {
                $table->string('pekerjaan', 100)->nullable()->after('alamat_detail');
            }
        });
    }

    public function down(): void
    {
        Schema::table('complaint_applicants', function (Blueprint $table) {
            if (Schema::hasColumn('complaint_applicants', 'pekerjaan')) {
                $table->dropColumn('pekerjaan');
            }
        });
    }
};