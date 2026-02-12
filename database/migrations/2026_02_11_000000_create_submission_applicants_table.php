<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_applicants', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('submission_id')->unique();
            $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('cascade');

            $table->string('nama_lengkap', 255);
            $table->string('nik', 16);
            $table->string('email', 255)->nullable();
            $table->string('phone', 255);

            $table->text('alamat_detail')->nullable();
            $table->string('kabupaten_kode', 50)->nullable();
            $table->string('kabupaten_nama', 255)->nullable();

            $table->string('kecamatan_kode', 50)->nullable();
            $table->string('kecamatan_nama', 255)->nullable();

            $table->string('desa_kode', 50)->nullable();
            $table->string('desa_nama', 255)->nullable();

            $table->string('provinsi', 255)->default('Jawa Tengah');

            $table->boolean('is_kelurahan')->default(false);

            $table->string('foto_ktp')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_applicants');
    }
};