<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'institution_id', 'document_type_id', 'person_id', 'file_path',
        'original_filename', 'mime_type', 'file_size', 'issued_at', 'expires_at',
        'protocol_number', 'notes', 'is_public', 'is_current', 'uploaded_by',
    ];

    protected $casts = [
        'issued_at'  => 'date',
        'expires_at' => 'date',
        'is_public'  => 'boolean',
        'is_current' => 'boolean',
        'file_size'  => 'integer',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function notificationLogs()
    {
        return $this->hasMany(NotificationLog::class);
    }

    // Status: valido | vence_em_breve (≤10d) | vence_urgente (≤5d) | vence_critico (≤1d) | vencido | sem_validade
    public function getStatusAttribute(): string
    {
        if (is_null($this->expires_at)) {
            return 'sem_validade';
        }

        $today = Carbon::today();

        if ($this->expires_at->lt($today)) {
            return 'vencido';
        }

        $dias = (int) $today->diffInDays($this->expires_at);

        if ($dias <= 1)  return 'vence_critico';
        if ($dias <= 5)  return 'vence_urgente';
        if ($dias <= 10) return 'vence_em_breve';

        return 'valido';
    }

    public function getStatusLabelAttribute(): string
    {
        $dias = $this->expires_at ? (int) Carbon::today()->diffInDays($this->expires_at) : null;

        return match($this->status) {
            'valido'         => 'Válido',
            'vence_em_breve' => "Vence em {$dias}d",
            'vence_urgente'  => "Vence em {$dias}d",
            'vence_critico'  => $dias === 0 ? 'Vence hoje' : 'Vence amanhã',
            'vencido'        => 'Vencido',
            'sem_validade'   => 'Sem vencimento',
            default          => '—',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'valido'         => 'green',
            'vence_em_breve' => 'yellow',
            'vence_urgente'  => 'orange',
            'vence_critico'  => 'red',
            'vencido'        => 'red',
            'sem_validade'   => 'gray',
            default          => 'gray',
        };
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeExpiringSoon($query, int $days = 10)
    {
        return $query->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }
}
