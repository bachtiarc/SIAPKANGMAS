<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('submission_applicants', function (Blueprint $table) {
            $table->string('pekerjaan', 255)->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('submission_applicants', function (Blueprint $table) {
            $table->dropColumn('pekerjaan');
        });
    }
};