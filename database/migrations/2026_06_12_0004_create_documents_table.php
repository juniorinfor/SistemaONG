<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->nullable()->constrained('people')->nullOnDelete();
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime_type', 50)->nullable();
            $table->unsignedBigInteger('file_size')->nullable()->comment('bytes');
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('protocol_number')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_current')->default(true)->comment('versão vigente do tipo');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['institution_id', 'document_type_id', 'is_current']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
