<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditalAttachment extends Model
{
    protected $fillable = ['edital_id', 'nome', 'arquivo_path', 'link', 'tipo'];

    public function edital(): BelongsTo
    {
        return $this->belongsTo(Edital::class);
    }

    public function getTipoLabelAttribute(): string
    {
        return match($this->tipo) {
            'edital'     => 'Edital',
            'modelo'     => 'Modelo',
            'formulario' => 'Formulário',
            default      => 'Anexo',
        };
    }
}
