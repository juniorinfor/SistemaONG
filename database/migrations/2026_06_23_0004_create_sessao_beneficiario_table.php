<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessao_beneficiario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sessao_id')->constrained('sessoes_acao')->cascadeOnDelete();
            $table->foreignId('beneficiario_id')->constrained('beneficiarios')->cascadeOnDelete();
            $table->boolean('presente')->default(true);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->unique(['sessao_id', 'beneficiario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessao_beneficiario');
    }
};
