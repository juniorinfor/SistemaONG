<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('nome', 200);
            $table->date('data_nascimento')->nullable();
            $table->string('cpf', 14)->nullable();
            $table->string('rg', 20)->nullable();
            $table->enum('genero', ['masculino', 'feminino', 'nao_binario', 'prefiro_nao_informar'])->default('prefiro_nao_informar');
            $table->enum('raca_cor', ['branca', 'preta', 'parda', 'amarela', 'indigena', 'nao_informado'])->default('nao_informado');
            // Responsável (obrigatório para menores ou quem não tem CPF)
            $table->string('nome_responsavel', 200)->nullable();
            $table->string('cpf_responsavel', 14)->nullable();
            $table->string('parentesco', 50)->nullable(); // mãe, pai, avó, tutor...
            // Contato
            $table->string('telefone', 20)->nullable();
            $table->string('email', 200)->nullable();
            // Endereço
            $table->string('cep', 10)->nullable();
            $table->string('endereco', 300)->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('bairro', 100)->nullable();
            $table->string('cidade', 100)->nullable()->default('Jaboatão dos Guararapes');
            // Status e extras
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->text('observacoes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiarios');
    }
};
