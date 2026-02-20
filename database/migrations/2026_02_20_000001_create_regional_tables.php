<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reg_provinces', function (Blueprint $table) {
            $table->unsignedBigInteger('code')->primary(); // contoh: 33
            $table->string('name', 255);
        });

        Schema::create('reg_regencies', function (Blueprint $table) {
            $table->unsignedBigInteger('code')->primary();        // contoh: 3374
            $table->unsignedBigInteger('province_code')->index(); // contoh: 33
            $table->string('name', 255);
        });

        Schema::create('reg_districts', function (Blueprint $table) {
            $table->unsignedBigInteger('code')->primary();        // contoh: 337401
            $table->unsignedBigInteger('regency_code')->index();  // contoh: 3374
            $table->string('name', 255);
        });

        Schema::create('reg_villages', function (Blueprint $table) {
            $table->unsignedBigInteger('code')->primary();        // contoh: 3374011001
            $table->unsignedBigInteger('district_code')->index(); // contoh: 337401
            $table->string('name', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reg_villages');
        Schema::dropIfExists('reg_districts');
        Schema::dropIfExists('reg_regencies');
        Schema::dropIfExists('reg_provinces');
    }
};