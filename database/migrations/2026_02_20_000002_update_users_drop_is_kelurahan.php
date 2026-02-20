<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_kelurahan')) {
                $table->dropColumn('is_kelurahan');
            }
        });

    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_kelurahan')) {
                $table->boolean('is_kelurahan')->nullable()->default(false);
            }
        });
    }
};