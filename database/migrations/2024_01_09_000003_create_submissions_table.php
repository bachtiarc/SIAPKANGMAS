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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            
            // Ticket Information - HANYA SATU FIELD
            $table->string('ticket_id')->unique(); // Format: PI20_001_JAN26
            
            // User Information
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Category Information
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            
            // Submission Details
            $table->string('title'); 
            $table->string('subject')->nullable(); // Subject same as title
            $table->text('description'); 
            $table->string('document_path')->nullable(); // Deprecated - kept for backward compatibility
            
            // Status Information
            $table->enum('status', ['pending', 'in_progress', 'completed', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Timestamps
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('category_id');
            $table->index('status');
            $table->index('ticket_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};