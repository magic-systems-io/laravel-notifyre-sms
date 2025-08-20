<?php

namespace Arbi\Notifyre\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotifyreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:160'],
            'sender' => ['nullable', 'string', 'max:255'],
            'recipients' => ['required', 'array'],
            'recipients.*' => ['required', 'string', 'max:255'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
