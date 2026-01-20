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
            
            if (!Schema::hasColumn('submissions', 'ticket_id')) {
                $table->string('ticket_id')->unique()->after('id');
            }
            
            if (!Schema::hasColumn('submissions', 'full_ticket_number')) {
                $table->string('full_ticket_number')->unique()->after('ticket_id');
            }
            
            if (!Schema::hasColumn('submissions', 'title')) {
                $table->string('title')->after('category_id');
            }
            
            if (!Schema::hasColumn('submissions', 'document_path')) {
                $table->string('document_path')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('submissions', 'submitted_at')) {
                $table->timestamp('submitted_at')->useCurrent()->after('handled_by');
            }
            
            // Update existing columns if needed
            // Note: Be careful with these, they might cause data loss
            
            // Add indexes for better performance
            if (!Schema::hasIndex('submissions', ['ticket_id'])) {
                $table->index('ticket_id');
            }
            
            if (!Schema::hasIndex('submissions', ['user_id'])) {
                $table->index('user_id');
            }
            
            if (!Schema::hasIndex('submissions', ['category_id'])) {
                $table->index('category_id');
            }
            
            if (!Schema::hasIndex('submissions', ['status'])) {
                $table->index('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn([
                'ticket_id',
                'full_ticket_number',
                'title',
                'document_path',
                'submitted_at'
            ]);
            
            $table->dropIndex(['ticket_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['status']);
        });
    }
};