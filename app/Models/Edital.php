<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Edital extends Model
{
    use SoftDeletes;

    protected $table = 'editais';

    protected $fillable = [
        'institution_id', 'titulo', 'area', 'fonte', 'fonte_id',
        'link_oficial', 'valor_min', 'valor_max', 'prazo_inscricao',
        'prazo_execucao', 'resumo', 'criterios', 'raw_text',
        'compatibility_score', 'compatibility_details', 'status', 'synced_at',
    ];

    protected $casts = [
        'valor_min'             => 'decimal:2',
        'valor_max'             => 'decimal:2',
        'prazo_inscricao'       => 'date',
        'prazo_execucao'        => 'date',
        'compatibility_details' => 'array',
        'synced_at'             => 'datetime',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(EditalAttachment::class);
    }

    // Editais com prazo de inscrição ainda no futuro ou hoje
    public function scopeAbertos(Builder $q): Builder
    {
        return $q->where('status', 'aberto')
                 ->where(fn($s) => $s->whereNull('prazo_inscricao')
                                     ->orWhere('prazo_inscricao', '>=', now()->toDateString()));
    }

    // Remove 7 dias após o prazo
    public function scopeExpirados(Builder $q): Builder
    {
        return $q->where('prazo_inscricao', '<', now()->subDays(7)->toDateString());
    }

    public function getPrazoStatusAttribute(): string
    {
        if (!$this->prazo_inscricao) return 'sem_prazo';
        $days = now()->diffInDays($this->prazo_inscricao, false);
        if ($days < 0)  return 'encerrado';
        if ($days <= 10) return 'urgente';
        if ($days <= 30) return 'breve';
        return 'ok';
    }

    public function getValorFormatadoAttribute(): ?string
    {
        if (!$this->valor_min && !$this->valor_max) return null;
        $fmt = fn($v) => 'R$ ' . number_format($v, 0, ',', '.');
        if ($this->valor_min && $this->valor_max && $this->valor_min != $this->valor_max) {
            return $fmt($this->valor_min) . ' – ' . $fmt($this->valor_max);
        }
        return $fmt($this->valor_max ?? $this->valor_min);
    }
}
