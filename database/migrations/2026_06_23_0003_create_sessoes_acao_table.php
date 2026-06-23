<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessoes_acao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acao_id')->constrained('acoes')->cascadeOnDelete();
            $table->date('data_execucao');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fim')->nullable();
            $table->string('local_override', 200)->nullable();     // sobrescreve local da ação
            $table->string('facilitador_override', 200)->nullable(); // sobrescreve responsável da ação
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessoes_acao');
    }
};
