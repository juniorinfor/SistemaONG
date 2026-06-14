<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'institution_id', 'edital_id', 'title', 'description', 'area',
        'status', 'valor_pleiteado', 'valor_aprovado',
        'submitted_at', 'approved_at', 'start_date', 'end_date', 'notes',
    ];

    protected $casts = [
        'submitted_at'    => 'date',
        'approved_at'     => 'date',
        'start_date'      => 'date',
        'end_date'        => 'date',
        'valor_pleiteado' => 'decimal:2',
        'valor_aprovado'  => 'decimal:2',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function edital(): BelongsTo
    {
        return $this->belongsTo(Edital::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'rascunho'      => 'Rascunho',
            'em_elaboracao' => 'Em elaboração',
            'submetido'     => 'Submetido',
            'aprovado'      => 'Aprovado',
            'reprovado'     => 'Reprovado',
            'em_execucao'   => 'Em execução',
            'concluido'     => 'Concluído',
            'cancelado'     => 'Cancelado',
            default         => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'rascunho'      => '#9e9e9e',
            'em_elaboracao' => '#1565c0',
            'submetido'     => '#e65100',
            'aprovado'      => '#00897b',
            'reprovado'     => '#c62828',
            'em_execucao'   => '#6a1b9a',
            'concluido'     => '#2e7d32',
            'cancelado'     => '#757575',
            default         => '#9e9e9e',
        };
    }

    public function getStatusBgAttribute(): string
    {
        return match($this->status) {
            'rascunho'      => '#f5f5f5',
            'em_elaboracao' => '#e3f2fd',
            'submetido'     => '#fff3e0',
            'aprovado'      => '#e8f8f5',
            'reprovado'     => '#fce4e4',
            'em_execucao'   => '#f3e5f5',
            'concluido'     => '#e8f5e9',
            'cancelado'     => '#f5f5f5',
            default         => '#f5f5f5',
        ];
    }

    public function getValorPleiteadoFormatadoAttribute(): ?string
    {
        if (is_null($this->valor_pleiteado)) return null;
        return 'R$ ' . number_format($this->valor_pleiteado, 2, ',', '.');
    }

    public function getValorAprovadoFormatadoAttribute(): ?string
    {
        if (is_null($this->valor_aprovado)) return null;
        return 'R$ ' . number_format($this->valor_aprovado, 2, ',', '.');
    }
}
