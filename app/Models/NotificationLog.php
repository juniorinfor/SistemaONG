<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    protected $fillable = [
        'institution_id', 'document_id', 'channel', 'notifiable_type',
        'notifiable_id', 'days_before_expiry', 'status', 'error_message', 'sent_at',
    ];

    protected $casts = [
        'sent_at'           => 'datetime',
        'days_before_expiry'=> 'integer',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
