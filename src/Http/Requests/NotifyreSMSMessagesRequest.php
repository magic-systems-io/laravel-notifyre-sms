<?php

namespace MagicSystemsIO\Notifyre\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;

class NotifyreSMSMessagesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:160'],
            'sender' => ['nullable', 'string', 'max:255'],
            'recipients' => ['required', 'array', 'min:1'],
            'recipients.*' => ['required', 'array', 'min:1'],
            'recipients.*.type' => ['required', 'string', Rule::enum(NotifyreRecipientTypes::class)],
            'recipients.*.value' => ['required', 'string', 'max:255'],
            'persist' => ['nullable', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
