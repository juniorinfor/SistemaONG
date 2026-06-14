<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('edital_id')->nullable()->constrained('editais')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('area')->nullable();
            $table->enum('status', [
                'rascunho',
                'em_elaboracao',
                'submetido',
                'aprovado',
                'reprovado',
                'em_execucao',
                'concluido',
                'cancelado',
            ])->default('rascunho');
            $table->decimal('valor_pleiteado', 15, 2)->nullable();
            $table->decimal('valor_aprovado',  15, 2)->nullable();
            $table->date('submitted_at')->nullable();
            $table->date('approved_at')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
