<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Beneficiario extends Model
{
    use SoftDeletes;

    protected $table = 'beneficiarios';

    protected $fillable = [
        'institution_id', 'nome', 'data_nascimento', 'cpf', 'rg',
        'genero', 'raca_cor',
        'nome_responsavel', 'cpf_responsavel', 'parentesco',
        'telefone', 'email', 'cep', 'endereco', 'numero', 'bairro', 'cidade',
        'status', 'observacoes',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function sessoes(): BelongsToMany
    {
        return $this->belongsToMany(SessaoAcao::class, 'sessao_beneficiario', 'beneficiario_id', 'sessao_id')
                    ->withPivot('presente', 'observacoes')
                    ->withTimestamps();
    }

    public function getIdadeAttribute(): ?int
    {
        return $this->data_nascimento ? $this->data_nascimento->age : null;
    }

    public function getIsMenorAttribute(): bool
    {
        if ($this->data_nascimento) {
            return $this->data_nascimento->age < 18;
        }
        return empty($this->cpf);
    }

    public function getGeneroLabelAttribute(): string
    {
        return match($this->genero) {
            'masculino'           => 'Masculino',
            'feminino'            => 'Feminino',
            'nao_binario'         => 'Não-binário',
            'prefiro_nao_informar'=> 'Prefiro não informar',
            default               => $this->genero,
        };
    }

    public function getRacaCorLabelAttribute(): string
    {
        return match($this->raca_cor) {
            'branca'       => 'Branca',
            'preta'        => 'Preta',
            'parda'        => 'Parda',
            'amarela'      => 'Amarela',
            'indigena'     => 'Indígena',
            'nao_informado'=> 'Não informado',
            default        => $this->raca_cor,
        };
    }
}
