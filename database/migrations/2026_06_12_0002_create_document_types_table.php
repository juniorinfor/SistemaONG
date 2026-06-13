<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('category'); // juridico, federal, estadual, municipal, contabil, titulacao, pessoal
            $table->enum('sphere', ['federal', 'estadual', 'municipal', 'interno'])->default('federal');
            $table->unsignedInteger('validity_days')->nullable()->comment('null = sem validade fixa');
            $table->boolean('requires_history')->default(false);
            $table->boolean('is_per_person')->default(false);
            $table->longText('instructions')->nullable()->comment('Markdown: como/onde obter');
            $table->string('official_url')->nullable();
            $table->boolean('is_public_by_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
