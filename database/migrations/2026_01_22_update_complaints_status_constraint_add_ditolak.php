<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE complaints DROP CONSTRAINT IF EXISTS complaints_status_check');

        DB::statement("
            ALTER TABLE complaints 
            ADD CONSTRAINT complaints_status_check 
            CHECK (status IN ('pending', 'diproses', 'selesai', 'ditolak'))
        ");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE complaints DROP CONSTRAINT IF EXISTS complaints_status_check');

        DB::statement("
            ALTER TABLE complaints 
            ADD CONSTRAINT complaints_status_check 
            CHECK (status IN ('pending', 'diproses', 'selesai'))
        ");
    }
};