<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop old constraint
        DB::statement('ALTER TABLE complaints DROP CONSTRAINT IF EXISTS complaints_status_check');
        
        // Add new constraint with 3 statuses (same as consultation)
        DB::statement("
            ALTER TABLE complaints 
            ADD CONSTRAINT complaints_status_check 
            CHECK (status IN ('pending', 'diproses', 'selesai'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new constraint
        DB::statement('ALTER TABLE complaints DROP CONSTRAINT IF EXISTS complaints_status_check');
        
        // Restore old constraint
        DB::statement("
            ALTER TABLE complaints 
            ADD CONSTRAINT complaints_status_check 
            CHECK (status IN ('pending', 'on_progress', 'completed', 'rejected'))
        ");
    }
};