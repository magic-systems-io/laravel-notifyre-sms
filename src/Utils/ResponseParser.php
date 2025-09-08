<?php

namespace MagicSystemsIO\Notifyre\Utils;

use MagicSystemsIO\Notifyre\DTO\SMS\InvalidNumber;
use MagicSystemsIO\Notifyre\DTO\SMS\Metadata;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponseBody;
use MagicSystemsIO\Notifyre\DTO\SMS\ResponsePayload;
use MagicSystemsIO\Notifyre\DTO\SMS\SmsRecipient;

class ResponseParser
{
    public static function parseSmsListResponse(array $smsMessages, int $statusCode): array
    {
        return array_map(
            fn ($item) => self::parseSmsResponse($item, $statusCode),
            array_values(self::groupSmsMessagesByID($smsMessages))
        );
    }

    public static function parseSmsResponse(array $responseData, int $statusCode): ?ResponseBody
    {
        if (empty($responseData)) {
            return null;
        }

        $payload = $responseData['payload'] ?? $responseData;
        $isFullResponse = isset($responseData['payload']);

        $invalidToNumbers = self::parseInvalidNumbers($payload);
        $recipients = self::parseRecipients($payload);
        $metadata = self::parseMetadata($payload);

        $responsePayload = new ResponsePayload(
            id:                  $payload['id'] ?? $payload['smsMessageID'] ?? '',
            friendlyID:          $payload['friendlyID'] ?? '',
            accountID:           $payload['accountID'] ?? '',
            createdBy:           $payload['createdBy'] ?? '',
            recipients:          $recipients,
            status:              $payload['status'] ?? '',
            totalCost:           (float) ($payload['totalCost'] ?? 0),
            metadata:            $metadata ?? new Metadata('', ''),
            createdDateUtc:      (int) ($payload['createdDateUtc'] ?? 0),
            submittedDateUtc:    (int) ($payload['submittedDateUtc'] ?? 0),
            completedDateUtc:    isset($payload['completedDateUtc']) ? (int) $payload['completedDateUtc'] : null,
            lastModifiedDateUtc: (int) ($payload['lastModifiedDateUtc'] ?? 0),
            campaignName:        $payload['campaignName'] ?? '',
            invalidToNumbers:    $invalidToNumbers,
        );

        return new ResponseBody(
            success:    $isFullResponse ? ($responseData['success'] ?? false) : true,
            statusCode: $isFullResponse ? ($responseData['statusCode'] ?? $statusCode) : $statusCode,
            message:    $isFullResponse ? ($responseData['message'] ?? '') : 'OK',
            payload:    $responsePayload,
            errors:     $isFullResponse ? ($responseData['errors'] ?? []) : []
        );
    }

    private static function parseInvalidNumbers(array $payload): array
    {
        if (isset($payload['invalidToNumbers']) && is_array($payload['invalidToNumbers'])) {
            $invalidToNumbers = array_map(fn ($invalidNumber) => new InvalidNumber(
                number:  $invalidNumber['number'] ?? '',
                message: $invalidNumber['message'] ?? ''
            ), $payload['invalidToNumbers']);
        }

        return $invalidToNumbers ?? [];
    }

    private static function parseRecipients(array $payload): array
    {
        if (isset($payload['recipients']) && is_array($payload['recipients'])) {
            $recipients = array_map(fn ($recipient) => self::parseRecipient($recipient), $payload['recipients']);
        } elseif (isset($payload['recipient']) && is_array($payload['recipient'])) {
            $recipients = [self::parseRecipient($payload['recipient'])];
        }

        return $recipients ?? [];
    }

    private static function parseRecipient(array $recipient): SmsRecipient
    {
        return new SmsRecipient(
            id:               $recipient['id'] ?? '',
            friendlyID:       $recipient['friendlyID'] ?? '',
            toNumber:         $recipient['toNumber'] ?? '',
            fromNumber:       $recipient['fromNumber'] ?? '',
            cost:             (float) ($recipient['cost'] ?? 0),
            messageParts:     (int) ($recipient['messageParts'] ?? 0),
            costPerPart:      (float) ($recipient['costPerPart'] ?? 0),
            status:           $recipient['status'] ?? '',
            statusMessage:    $recipient['statusMessage'] ?? '',
            deliveryStatus:   $recipient['deliveryStatus'] ?? null,
            queuedDateUtc:    (int) ($recipient['queuedDateUtc'] ?? 0),
            completedDateUtc: (int) ($recipient['completedDateUtc'] ?? 0),
        );
    }

    /**
     * Parse metadata from payload
     */
    private static function parseMetadata(array $payload): ?Metadata
    {
        if (isset($payload['metadata']) && is_array($payload['metadata'])) {
            return new Metadata(
                requestingUserId:    $payload['metadata']['requestingUserId'] ?? '',
                requestingUserEmail: $payload['metadata']['requestingUserEmail'] ?? '',
            );
        }

        return null;
    }

    /**
     * Group SMS messages by ID and merge recipients
     */
    private static function groupSmsMessagesByID(array $smsMessages): array
    {
        $grouped = [];

        foreach ($smsMessages as $message) {
            $payload = $message['payload'] ?? $message;
            $id = $payload['id'] ?? $payload['smsMessageID'] ?? 'N/A';

            if (!isset($grouped[$id])) {
                $grouped[$id] = $message;
            } else {
                $grouped[$id] = self::mergeMessageRecipients($grouped[$id], $message);
            }
        }

        return $grouped;
    }

    private static function mergeMessageRecipients(array $existingMessage, array $newMessage): array
    {
        $existing = $existingMessage['payload'] ?? $existingMessage;
        $newPayload = $newMessage['payload'] ?? $newMessage;

        $existingRecipients = self::normalizeRecipients($existing);
        $newRecipients = self::normalizeRecipients($newPayload);

        $existingIds = array_column($existingRecipients, 'id');

        foreach ($newRecipients as $recipient) {
            if (!in_array($recipient['id'] ?? '', $existingIds)) {
                $existingRecipients[] = $recipient;
            }
        }

        $existing['recipients'] = $existingRecipients;
        unset($existing['recipient']);

        if (isset($existingMessage['payload'])) {
            $existingMessage['payload'] = $existing;
        } else {
            $existingMessage = $existing;
        }

        return $existingMessage;
    }

    private static function normalizeRecipients(array $payload): array
    {
        if (isset($payload['recipients']) && is_array($payload['recipients'])) {
            return $payload['recipients'];
        }

        if (isset($payload['recipient'])) {
            return [$payload['recipient']];
        }

        return [];
    }
}
