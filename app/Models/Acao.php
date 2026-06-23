<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Acao extends Model
{
    use SoftDeletes;

    protected $table = 'acoes';

    protected $fillable = [
        'institution_id', 'project_id', 'titulo', 'descricao', 'tipo',
        'local', 'responsavel_nome', 'responsavel_cargo',
        'carga_horaria_sessao', 'status',
        'objetivos', 'metodologia', 'observacoes',
    ];

    protected $casts = [
        'carga_horaria_sessao' => 'float',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function sessoes(): HasMany
    {
        return $this->hasMany(SessaoAcao::class, 'acao_id')->orderBy('data_execucao');
    }

    public function getTipoLabelAttribute(): string
    {
        return match($this->tipo) {
            'oficina'               => 'Oficina',
            'palestra'              => 'Palestra',
            'atendimento_individual'=> 'Atendimento Individual',
            'grupo'                 => 'Grupo',
            'capacitacao'           => 'Capacitação',
            'evento'                => 'Evento',
            'visita_domiciliar'     => 'Visita Domiciliar',
            'reuniao'               => 'Reunião',
            default                 => 'Outro',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'planejada'    => 'Planejada',
            'em_andamento' => 'Em andamento',
            'concluida'    => 'Concluída',
            'cancelada'    => 'Cancelada',
            default        => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'planejada'    => '#1565c0',
            'em_andamento' => '#e65100',
            'concluida'    => '#2e7d32',
            'cancelada'    => '#7f0000',
            default        => '#666',
        };
    }

    public function getStatusBgAttribute(): string
    {
        return match($this->status) {
            'planejada'    => '#e3f2fd',
            'em_andamento' => '#fff8e1',
            'concluida'    => '#e8f5e9',
            'cancelada'    => '#fce4e4',
            default        => '#f5f5f5',
        };
    }

    public function getTotalSessoesAttribute(): int
    {
        return $this->sessoes()->count();
    }

    public function getTotalBeneficiariosUnicosAttribute(): int
    {
        return \DB::table('sessao_beneficiario')
            ->join('sessoes_acao', 'sessoes_acao.id', '=', 'sessao_beneficiario.sessao_id')
            ->where('sessoes_acao.acao_id', $this->id)
            ->where('sessao_beneficiario.presente', true)
            ->distinct('sessao_beneficiario.beneficiario_id')
            ->count('sessao_beneficiario.beneficiario_id');
    }

    public function getTotalPresencasAttribute(): int
    {
        return \DB::table('sessao_beneficiario')
            ->join('sessoes_acao', 'sessoes_acao.id', '=', 'sessao_beneficiario.sessao_id')
            ->where('sessoes_acao.acao_id', $this->id)
            ->where('sessao_beneficiario.presente', true)
            ->count();
    }

    public function getCargaHorariaTotalAttribute(): ?float
    {
        if (!$this->carga_horaria_sessao) return null;
        return round($this->carga_horaria_sessao * $this->total_sessoes, 1);
    }
}
