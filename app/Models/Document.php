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

    // Status calculado: valido | vence_em_breve | vencido | sem_validade
    public function getStatusAttribute(): string
    {
        if (is_null($this->expires_at)) {
            return 'sem_validade';
        }

        $today = Carbon::today();
        $warnDays = config('documents.warn_days_before_expiry', 30);

        if ($this->expires_at->isPast()) {
            return 'vencido';
        }

        if ($this->expires_at->diffInDays($today) <= $warnDays) {
            return 'vence_em_breve';
        }

        return 'valido';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'valido'        => 'Válido',
            'vence_em_breve'=> 'Vence em breve',
            'vencido'       => 'Vencido',
            'sem_validade'  => 'Sem vencimento',
            default         => '—',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'valido'        => 'green',
            'vence_em_breve'=> 'yellow',
            'vencido'       => 'red',
            'sem_validade'  => 'gray',
            default         => 'gray',
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

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }
}
