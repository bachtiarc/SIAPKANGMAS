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
            
            // Ticket Information
            $table->string('ticket_id')->unique(); // Format: PI02_03_JAN26
            $table->string('full_ticket_number')->unique(); // Format: PI.01.106.12012026_010
            
            // User Information
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Category Information
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            
            // Submission Details
            $table->string('title'); 
            $table->text('description'); 
            $table->string('document_path')->nullable(); 
            
            // Status Information
            $table->enum('status', ['pending', 'in_progress', 'completed', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable(); // Catatan dari admin
            $table->foreignId('handled_by')->nullable()->constrained('users')->onDelete('set null'); // Admin yang handle
            
            // Timestamps
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Indexes for faster queries
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