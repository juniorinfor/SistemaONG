<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('titulo');
            $table->string('area')->nullable();
            $table->string('fonte')->default('manual'); // transferegov, iati, dou, manual
            $table->string('fonte_id')->nullable();     // ID externo para evitar duplicatas
            $table->string('link_oficial')->nullable();
            $table->decimal('valor_min', 14, 2)->nullable();
            $table->decimal('valor_max', 14, 2)->nullable();
            $table->date('prazo_inscricao')->nullable();
            $table->date('prazo_execucao')->nullable();
            $table->text('resumo')->nullable();         // Resumo em PT extraído pela IA
            $table->text('criterios')->nullable();      // Requisitos/critérios em PT
            $table->text('raw_text')->nullable();       // Texto bruto (usado na extração, descartável)
            $table->integer('compatibility_score')->nullable(); // 0–100
            $table->json('compatibility_details')->nullable();  // {matched:[], missing:[]}
            $table->enum('status', ['aberto', 'encerrado', 'processando'])->default('aberto');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['institution_id', 'status']);
            $table->index('prazo_inscricao');
        });

        Schema::create('edital_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edital_id')->constrained('editais')->cascadeOnDelete();
            $table->string('nome');
            $table->string('arquivo_path')->nullable();
            $table->string('link')->nullable();
            $table->string('tipo')->default('anexo'); // edital, anexo, modelo, formulario
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edital_attachments');
        Schema::dropIfExists('editais');
    }
};
