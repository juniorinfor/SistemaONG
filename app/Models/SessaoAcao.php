<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SessaoAcao extends Model
{
    protected $table = 'sessoes_acao';

    protected $fillable = [
        'acao_id', 'data_execucao', 'hora_inicio', 'hora_fim',
        'local_override', 'facilitador_override', 'observacoes',
    ];

    protected $casts = [
        'data_execucao' => 'date',
    ];

    public function acao(): BelongsTo
    {
        return $this->belongsTo(Acao::class, 'acao_id');
    }

    public function beneficiarios(): BelongsToMany
    {
        return $this->belongsToMany(Beneficiario::class, 'sessao_beneficiario', 'sessao_id', 'beneficiario_id')
                    ->withPivot('presente', 'observacoes')
                    ->withTimestamps();
    }

    public function getTotalPresentesAttribute(): int
    {
        return $this->beneficiarios()->wherePivot('presente', true)->count();
    }

    public function getDuracaoAttribute(): ?string
    {
        if (!$this->hora_inicio || !$this->hora_fim) return null;
        $ini = \Carbon\Carbon::parse($this->hora_inicio);
        $fim = \Carbon\Carbon::parse($this->hora_fim);
        $min = $ini->diffInMinutes($fim);
        $h   = intdiv($min, 60);
        $m   = $min % 60;
        return $h > 0 ? "{$h}h" . ($m > 0 ? "{$m}min" : '') : "{$m}min";
    }
}
