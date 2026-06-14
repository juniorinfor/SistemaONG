<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectAttachment extends Model
{
    protected $fillable = [
        'project_id', 'nome', 'arquivo_path', 'mime_type', 'file_size', 'tipo',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getTipoLabelAttribute(): string
    {
        return match($this->tipo) {
            'proposta'   => 'Proposta',
            'plano'      => 'Plano de trabalho',
            'orcamento'  => 'Orçamento',
            'relatorio'  => 'Relatório',
            'anexo'      => 'Anexo',
            default      => 'Documento',
        };
    }

    public function getTamanhoFormatadoAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 1, ',', '.') . ' MB';
        if ($bytes >= 1024)    return number_format($bytes / 1024, 0, ',', '.') . ' KB';
        return $bytes . ' B';
    }

    public function getIconeAttribute(): string
    {
        return match(true) {
            str_contains($this->mime_type ?? '', 'pdf')   => '📄',
            str_contains($this->mime_type ?? '', 'word'),
            str_contains($this->mime_type ?? '', 'document') => '📝',
            str_contains($this->mime_type ?? '', 'sheet'),
            str_contains($this->mime_type ?? '', 'excel')  => '📊',
            str_contains($this->mime_type ?? '', 'image')  => '🖼️',
            default => '📎',
        };
    }
}
