<?php

namespace MagicSystemsIO\Notifyre\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSmsMessageRecipient;

class NotifyreRecipients extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'tmp_id',
        'type',
        'value',
    ];

    protected $casts = [
        'id' => 'string',
        'tmp_id' => 'string',
        'type' => 'string',
        'value' => 'string',
    ];

    public function notifyreSmsMessageRecipients(): HasMany
    {
        return $this->hasMany(NotifyreSmsMessageRecipient::class, 'recipient_id');
    }
}
