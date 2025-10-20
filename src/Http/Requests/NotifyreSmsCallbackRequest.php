<?php

namespace MagicSystemsIO\Notifyre\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use MagicSystemsIO\Notifyre\DTO\SMS\SmsRecipient;

class NotifyreSmsCallbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->verifySignature();
    }

    /**
     * Verify the webhook signature from Notifyre.
     *
     * The Notifyre-Signature header contains: t=<timestamp>,v=<signature>
     *
     * Steps:
     * 1. Extract timestamp (t) and signature (v) from header
     * 2. Create signed payload: timestamp + "." + request body (JSON)
     * 3. Compute HMAC-SHA256 using webhook secret
     * 4. Compare computed signature with received signature
     * 5. Verify timestamp is within tolerance (default 5 minutes)
     */
    protected function verifySignature(): bool
    {
        $webhookSecret = config('notifyre.webhook.secret');

        if (empty($webhookSecret)) {
            Log::channel('notifyre')->warning('Webhook signature verification skipped: no secret configured');

            return true;
        }

        $signatureHeader = $this->header('Notifyre-Signature');

        if (empty($signatureHeader)) {
            Log::channel('notifyre')->error('Webhook signature verification failed: missing Notifyre-Signature header');

            return false;
        }

        $elements = explode(',', $signatureHeader);
        $timestamp = null;
        $signature = null;

        foreach ($elements as $element) {
            [$key, $value] = explode('=', $element, 2);
            if ($key === 't') {
                $timestamp = $value;
            } elseif ($key === 'v') {
                $signature = $value;
            }
        }

        if (empty($timestamp) || empty($signature)) {
            Log::channel('notifyre')->error('Webhook signature verification failed: invalid signature format');

            return false;
        }

        $tolerance = config('notifyre.webhook.signature_tolerance', 300);
        $currentTime = time();

        if (abs($currentTime - (int) $timestamp) > $tolerance) {
            Log::channel('notifyre')->error('Webhook signature verification failed: timestamp outside tolerance', [
                'received_timestamp' => $timestamp,
                'current_timestamp' => $currentTime,
                'tolerance' => $tolerance,
            ]);

            return false;
        }

        $payload = $timestamp . '.' . $this->getContent();

        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

        if (!hash_equals($expectedSignature, $signature)) {
            Log::channel('notifyre')->error('Webhook signature verification failed: signature mismatch');

            return false;
        }

        Log::channel('notifyre')->debug('Webhook signature verified successfully');

        return true;
    }

    public function rules(): array
    {
        $base = [
            'Event' => ['required', 'string', 'in:sms_sent,sms_received,fax_sent,fax_received,mms_received'],
            'Timestamp' => ['required', 'integer'],
            'Payload' => ['required', 'array'],
        ];

        $payload = [
            'Payload.ID' => ['required', 'string'],
            'Payload.FriendlyID' => ['nullable', 'string'],
            'Payload.AccountID' => ['required', 'string'],
            'Payload.CreatedBy' => ['required', 'string'],
            'Payload.Status' => ['required', 'string', 'in:draft,queued,completed,Failed,warning'],
            'Payload.TotalCost' => ['required', 'numeric'],
            'Payload.CreatedDateUtc' => ['required', 'integer'],
            'Payload.SubmittedDateUtc' => ['required', 'integer'],
            'Payload.CompletedDateUtc' => ['nullable', 'integer'],
            'Payload.LastModifiedDateUtc' => ['nullable', 'integer'],
        ];

        $recipient = [
            'Payload.Recipient' => ['required', 'array'],
            'Payload.Recipient.ID' => ['required', 'string'],
            'Payload.Recipient.ToNumber' => ['required', 'string'],
            'Payload.Recipient.FromNumber' => ['required', 'string'],
            'Payload.Recipient.Message' => ['nullable', 'string'],
            'Payload.Recipient.Cost' => ['required', 'numeric'],
            'Payload.Recipient.MessageParts' => ['required', 'integer'],
            'Payload.Recipient.CostPerPart' => ['required', 'numeric'],
            'Payload.Recipient.Status' => ['required', 'string'],
            'Payload.Recipient.StatusMessage' => ['nullable', 'string'],
            'Payload.Recipient.DeliveryStatus' => ['nullable', 'string'],
            'Payload.Recipient.QueuedDateUtc' => ['nullable', 'integer'],
            'Payload.Recipient.CompletedDateUtc' => ['nullable', 'integer'],
        ];

        $metadata = [
            'Payload.Metadata' => ['nullable', 'array'],
            'Payload.Metadata.requestingUserId' => ['nullable', 'string'],
            'Payload.Metadata.requestingUserEmail' => ['nullable', 'string'],
        ];

        return [...$base, ...$payload, ...$recipient, ...$metadata];
    }

    public function getRecipient(): SmsRecipient
    {
        $recipient = $this->validated('Payload.Recipient');

        return new SmsRecipient(
            id: $recipient['ID'],
            friendlyID: $this->validated('Payload.FriendlyID') ?? $this->validated('Payload.ID'),
            toNumber: $recipient['ToNumber'],
            fromNumber: $recipient['FromNumber'],
            cost: $recipient['Cost'],
            messageParts: $recipient['MessageParts'],
            costPerPart: $recipient['CostPerPart'],
            status: $recipient['Status'],
            statusMessage: $recipient['StatusMessage'] ?? '',
            deliveryStatus: $recipient['DeliveryStatus'] ?? null,
            queuedDateUtc: $recipient['QueuedDateUtc'] ?? 0,
            completedDateUtc: $recipient['CompletedDateUtc'] ?? 0,
        );
    }
}
