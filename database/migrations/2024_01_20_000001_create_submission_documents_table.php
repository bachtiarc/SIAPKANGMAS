<?php
// database/migrations/2024_01_20_000001_create_submission_documents_table.php
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
        Schema::create('submission_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->onDelete('cascade');
            $table->string('original_name'); 
            $table->string('file_path'); 
            $table->string('file_type'); 
            $table->unsignedBigInteger('file_size'); 
            $table->timestamps();
            
            // Index untuk query yang lebih cepat
            $table->index('submission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_documents');
    }
};