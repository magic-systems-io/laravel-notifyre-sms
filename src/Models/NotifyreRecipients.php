<?php

namespace MagicSystemsIO\Notifyre\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MagicSystemsIO\Notifyre\Database\Factories\NotifyreRecipientsFactory;
use MagicSystemsIO\Notifyre\Models\JunctionTables\NotifyreSmsMessageRecipient;

class NotifyreRecipients extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'type',
        'value',
    ];

    protected $casts = [
        'id' => 'string',
        'type' => 'string',
        'value' => 'string',
    ];

    protected static function newFactory(): NotifyreRecipientsFactory
    {
        return NotifyreRecipientsFactory::new();
    }

    public function smsMessages(): BelongsToMany
    {
        return $this->belongsToMany(
            NotifyreSmsMessages::class,
            NotifyreSmsMessageRecipient::class,
            'recipient_id',
            'sms_message_id',
        )->withPivot('sent');
    }
}
