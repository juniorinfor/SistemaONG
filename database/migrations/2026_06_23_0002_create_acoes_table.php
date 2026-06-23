<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('titulo', 300);
            $table->text('descricao')->nullable();
            $table->enum('tipo', [
                'oficina', 'palestra', 'atendimento_individual', 'grupo',
                'capacitacao', 'evento', 'visita_domiciliar', 'reuniao', 'outro'
            ])->default('outro');
            $table->string('local', 200)->nullable();
            $table->string('responsavel_nome', 200)->nullable();
            $table->string('responsavel_cargo', 100)->nullable();
            $table->decimal('carga_horaria_sessao', 4, 1)->nullable(); // horas por encontro
            $table->enum('status', ['planejada', 'em_andamento', 'concluida', 'cancelada'])->default('planejada');
            $table->text('objetivos')->nullable();
            $table->text('metodologia')->nullable();
            $table->text('observacoes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acoes');
    }
};
