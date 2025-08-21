<?php

namespace Arbi\Notifyre\Http\Requests;

use Arbi\Notifyre\Enums\NotifyreRecipientTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NotifyreSMSMessagesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:160'],
            'sender' => ['nullable', 'string', 'max:255'],
            'recipients' => ['required', 'array'],
            'recipients.*' => ['required', 'array'],
            'recipients.*.type' => ['required', 'string', Rule::enum(NotifyreRecipientTypes::class)],
            'recipients.*.value' => ['required', 'string', 'max:255'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
