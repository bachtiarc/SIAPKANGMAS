<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL specific: Drop constraint lama dan buat ulang
        // Kita ubah kolom jadi string biasa dulu, lalu kembalikan ke enum (cara aman di Laravel+PgSQL)

        DB::statement("ALTER TABLE submissions DROP CONSTRAINT IF EXISTS submissions_status_check");

        // Opsional: Jika ingin memastikan tipe kolom benar
        DB::statement("ALTER TABLE submissions ALTER COLUMN status TYPE VARCHAR(255)");

        // Tambahkan constraint baru yang benar
        DB::statement("ALTER TABLE submissions ADD CONSTRAINT submissions_status_check CHECK (status IN ('pending', 'in_progress', 'completed', 'rejected'))");
    }

    public function down(): void
    {
        // Kembalikan jika perlu (biasanya tidak perlu rollback untuk fix ini)
    }
};