<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE consultations DROP CONSTRAINT IF EXISTS consultations_consultation_type_check');
        
        DB::statement("ALTER TABLE consultations ADD CONSTRAINT consultations_consultation_type_check CHECK (consultation_type IN ('konsultasi', 'Konsultasi', 'pengaduan', 'Pengaduan'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE consultations DROP CONSTRAINT IF EXISTS consultations_consultation_type_check');
        DB::statement("ALTER TABLE consultations ADD CONSTRAINT consultations_consultation_type_check CHECK (consultation_type = 'konsultasi')");
    }
};