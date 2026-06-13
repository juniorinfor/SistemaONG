<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id', 'name', 'category', 'sphere', 'validity_days',
        'requires_history', 'is_per_person', 'instructions', 'official_url',
        'is_public_by_default', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'validity_days'       => 'integer',
        'requires_history'    => 'boolean',
        'is_per_person'       => 'boolean',
        'is_public_by_default'=> 'boolean',
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

    public function currentDocument(): HasMany
    {
        return $this->hasMany(Document::class)->where('is_current', true)->latest('expires_at');
    }

    public function checklists(): BelongsToMany
    {
        return $this->belongsToMany(Checklist::class, 'checklist_items')
                    ->withPivot('is_required', 'sort_order')
                    ->withTimestamps();
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(ChecklistItem::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'juridico'   => 'Jurídico / Institucional',
            'federal'    => 'Certidões Federais',
            'estadual'   => 'Certidões Estaduais (PE)',
            'municipal'  => 'Certidões Municipais',
            'contabil'   => 'Contábil / Financeiro',
            'titulacao'  => 'Titulações e Registros',
            'pessoal'    => 'Documentos Pessoais',
            default      => ucfirst($this->category),
        };
    }
}
