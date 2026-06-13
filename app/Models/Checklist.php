<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Checklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id', 'name', 'slug', 'description', 'legal_basis', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ChecklistItem::class)->orderBy('sort_order');
    }

    public function documentTypes(): BelongsToMany
    {
        return $this->belongsToMany(DocumentType::class, 'checklist_items')
                    ->withPivot('is_required', 'sort_order')
                    ->withTimestamps();
    }

    // Retorna % de prontidão baseado em documentos vigentes e válidos
    public function getReadinessPercentage(Institution $institution): int
    {
        $required = $this->items()->where('is_required', true)->count();
        if ($required === 0) return 100;

        $typeIds = $this->items()->where('is_required', true)->pluck('document_type_id');
        $covered = Document::where('institution_id', $institution->id)
            ->whereIn('document_type_id', $typeIds)
            ->where('is_current', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->distinct('document_type_id')
            ->count('document_type_id');

        return (int) round(($covered / $required) * 100);
    }
}
