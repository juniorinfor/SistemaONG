<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id', 'name', 'cpf', 'rg', 'role', 'type',
        'mandate_start', 'mandate_end', 'works_with_children',
        'email', 'phone', 'address', 'is_active',
    ];

    protected $casts = [
        'mandate_start'       => 'date',
        'mandate_end'         => 'date',
        'works_with_children' => 'boolean',
        'is_active'           => 'boolean',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'diretoria'   => 'Diretoria',
            'voluntario'  => 'Voluntário(a)',
            'colaborador' => 'Colaborador(a)',
            default       => ucfirst($this->type),
        };
    }

    public function isMandateActive(): bool
    {
        if (is_null($this->mandate_end)) {
            return true;
        }
        return $this->mandate_end->isFuture();
    }
}
