<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'cnpj', 'slug', 'address', 'city', 'state',
        'phone', 'email', 'website', 'logo_path', 'mission', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function documentTypes(): HasMany
    {
        return $this->hasMany(DocumentType::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(Checklist::class);
    }
}
