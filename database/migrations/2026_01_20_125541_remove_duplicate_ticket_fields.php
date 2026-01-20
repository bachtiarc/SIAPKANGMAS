<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            // Drop duplicate columns if they exist
            if (Schema::hasColumn('submissions', 'full_ticket_number')) {
                $table->dropColumn('full_ticket_number');
            }
            
            if (Schema::hasColumn('submissions', 'ticket_number')) {
                $table->dropColumn('ticket_number');
            }
            
            // Add subject column if not exists
            if (!Schema::hasColumn('submissions', 'subject')) {
                $table->string('subject')->nullable()->after('title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            // Restore columns if needed
            if (!Schema::hasColumn('submissions', 'full_ticket_number')) {
                $table->string('full_ticket_number')->unique()->nullable();
            }
            
            if (!Schema::hasColumn('submissions', 'ticket_number')) {
                $table->string('ticket_number')->nullable();
            }
            
            if (Schema::hasColumn('submissions', 'subject')) {
                $table->dropColumn('subject');
            }
        });
    }
};