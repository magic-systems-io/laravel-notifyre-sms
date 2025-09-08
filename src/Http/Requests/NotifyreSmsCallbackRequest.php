<?php

namespace MagicSystemsIO\Notifyre\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use MagicSystemsIO\Notifyre\DTO\SMS\InvalidNumber;
use MagicSystemsIO\Notifyre\DTO\SMS\Metadata;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponsePayload;
use MagicSystemsIO\Notifyre\DTO\SMS\SmsRecipient;

class NotifyreSmsCallbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'success' => ['required', 'boolean'],
            'status_code' => ['required', 'integer'],
            'message' => ['required', 'string'],
            'payload' => ['required', 'array'],
            'payload.id' => ['required', 'string'],
            'payload.friendly_id' => ['required', 'string'],
            'payload.account_id' => ['required', 'string'],
            'payload.created_by' => ['required', 'string'],
            'payload.recipients' => ['required', 'array'],
            'payload.recipients.*.id' => ['required', 'string'],
            'payload.recipients.*.friendly_id' => ['required', 'string'],
            'payload.recipients.*.to_number' => ['required', 'string'],
            'payload.recipients.*.from_number' => ['required', 'string'],
            'payload.recipients.*.cost' => ['required', 'numeric'],
            'payload.recipients.*.message_parts' => ['required', 'integer'],
            'payload.recipients.*.cost_per_part' => ['required', 'numeric'],
            'payload.recipients.*.status' => ['required', 'string'],
            'payload.recipients.*.status_message' => ['required', 'string'],
            'payload.recipients.*.delivery_status' => ['nullable', 'string'],
            'payload.recipients.*.queued_date_utc' => ['required', 'integer'],
            'payload.recipients.*.completed_date_utc' => ['required', 'integer'],
            'payload.status' => ['required', 'string'],
            'payload.total_cost' => ['required', 'numeric'],
            'payload.metadata' => ['required', 'array'],
            'payload.metadata.requesting_user_id' => ['required', 'string'],
            'payload.metadata.requesting_user_email' => ['required', 'string'],
            'payload.created_date_utc' => ['required', 'integer'],
            'payload.submitted_date_utc' => ['required', 'integer'],
            'payload.completed_date_utc' => ['nullable', 'integer'],
            'payload.last_modified_date_utc' => ['required', 'integer'],
            'payload.campaign_name' => ['required', 'string'],
            'payload.invalid_to_numbers' => ['array'],
            'payload.invalid_to_numbers.*.number' => ['required', 'string'],
            'payload.invalid_to_numbers.*.message' => ['required', 'string'],
            'errors' => ['array'],
        ];
    }

    /**
     * Get the validated data and convert it to ResponseBody DTO.
     */
    public function toResponseBody(): ResponseBody
    {
        $validated = $this->validated();

        $recipients = array_map(function ($recipient) {
            return new SmsRecipient(
                id: $recipient['id'],
                friendlyID: $recipient['friendly_id'],
                toNumber: $recipient['to_number'],
                fromNumber: $recipient['from_number'],
                cost: $recipient['cost'],
                messageParts: $recipient['message_parts'],
                costPerPart: $recipient['cost_per_part'],
                status: $recipient['status'],
                statusMessage: $recipient['status_message'],
                deliveryStatus: $recipient['delivery_status'] ?? null,
                queuedDateUtc: $recipient['queued_date_utc'],
                completedDateUtc: $recipient['completed_date_utc'],
            );
        }, $validated['payload']['recipients']);

        $invalidNumbers = array_map(function ($invalidNumber) {
            return new InvalidNumber(
                number: $invalidNumber['number'],
                message: $invalidNumber['message'],
            );
        }, $validated['payload']['invalid_to_numbers'] ?? []);

        $metadata = new Metadata(
            requestingUserId: $validated['payload']['metadata']['requesting_user_id'],
            requestingUserEmail: $validated['payload']['metadata']['requesting_user_email'],
        );

        $payload = new ResponsePayload(
            id: $validated['payload']['id'],
            friendlyID: $validated['payload']['friendly_id'],
            accountID: $validated['payload']['account_id'],
            createdBy: $validated['payload']['created_by'],
            recipients: $recipients,
            status: $validated['payload']['status'],
            totalCost: $validated['payload']['total_cost'],
            metadata: $metadata,
            createdDateUtc: $validated['payload']['created_date_utc'],
            submittedDateUtc: $validated['payload']['submitted_date_utc'],
            completedDateUtc: $validated['payload']['completed_date_utc'] ?? null,
            lastModifiedDateUtc: $validated['payload']['last_modified_date_utc'],
            campaignName: $validated['payload']['campaign_name'],
            invalidToNumbers: $invalidNumbers,
        );

        return new ResponseBody(
            success: $validated['success'],
            statusCode: $validated['status_code'],
            message: $validated['message'],
            payload: $payload,
            errors: $validated['errors'] ?? [],
        );
    }
}
