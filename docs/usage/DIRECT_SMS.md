# Direct SMS

Send SMS messages immediately using the `notifyre()` helper function with rich DTO support.

## Quick Example

```php
use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\Enums\NotifyreRecipientTypes;

notifyre()->send(new RequestBodyDTO(
    body: 'Hello World!',
    recipients: [new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890')]
));
```

## How It Works

The `notifyre()` helper function returns the NotifyreService, which:

1. Creates the appropriate driver (SMS or Log)
2. Sends your message through that driver
3. Returns a `ResponseBodyDTO` with delivery status
4. Handles any errors automatically

## Parameters

### RequestBodyDTO

- **`body`** (required) - The SMS message text
- **`recipients`** (required) - Array of Recipient objects
- **`from`** (optional) - The mobile phone number sending the SMS (empty for shared number)
- **`scheduledDate`** (optional) - Unix timestamp for scheduled sending
- **`addUnsubscribeLink`** (optional) - Whether to add opt-out link (default: false)
- **`callbackUrl`** (optional) - URL for delivery status webhooks
- **`metadata`** (optional) - Key-value pairs for additional information (max 50 keys)
- **`campaignName`** (optional) - Optional message reference for tracking

### Recipient

- **`type`** - Currently supports `'virtual_mobile_number'`, `'contact'`, and `'group'`
- **`value`** - The phone number, contact ID, or group ID

## Examples

### Basic SMS

```php
notifyre()->send(new RequestBodyDTO(
    body: 'Your order has shipped!',
    recipients: [new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+15551234567')]
));
```

### Multiple Recipients

```php
notifyre()->send(new RequestBodyDTO(
    body: 'Meeting reminder: 2 PM today',
    recipients: [
        new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+15551234567'),
        new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+15559876543'),
    ]
));
```

### Scheduled SMS

```php
notifyre()->send(new RequestBodyDTO(
    body: 'Happy Birthday! 🎉',
    recipients: [new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890')],
    scheduledDate: strtotime('tomorrow 9:00 AM'),
    campaignName: 'Birthday Campaign'
));
```

### SMS with Metadata and Callbacks

```php
notifyre()->send(new RequestBodyDTO(
    body: 'Your delivery is on its way!',
    recipients: [new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890')],
    from: '+1987654321',
    addUnsubscribeLink: true,
    callbackUrl: 'https://yourapp.com/sms-delivery-status',
    metadata: [
        'order_id' => 'ORD-12345',
        'customer_type' => 'premium',
        'delivery_method' => 'express',
        'estimated_delivery' => '2-3 business days'
    ],
    campaignName: 'Delivery Updates'
));
```

### Using Different Recipient Types

```php
// Send to a contact in your Notifyre account
notifyre()->send(new RequestBodyDTO(
    body: 'Welcome to our service!',
    recipients: [new Recipient(NotifyreRecipientTypes::CONTACT->value, 'contact_123')]
));

// Send to a group
notifyre()->send(new RequestBodyDTO(
    body: 'Group announcement: New features available!',
    recipients: [new Recipient(NotifyreRecipientTypes::GROUP->value, 'group_456')]
));
```

## Response Handling

The service now returns a `ResponseBodyDTO` that you can use to track delivery:

```php
$response = notifyre()->send(new RequestBodyDTO(
    body: 'Test message',
    recipients: [new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890')]
));

if ($response && $response->success) {
    echo "Message sent successfully!";
    echo "Message ID: " . $response->payload->smsMessageID;
    echo "Friendly ID: " . $response->payload->friendlyID;
    
    if (!empty($response->payload->invalidToNumbers)) {
        echo "Some numbers were invalid:";
        foreach ($response->payload->invalidToNumbers as $invalid) {
            echo "Number: {$invalid->number}, Reason: {$invalid->message}";
        }
    }
}
```

## DTO Features

### Arrayable Interface

All DTOs implement Laravel's `Arrayable` interface for easy data manipulation:

```php
$dto = new RequestBodyDTO(
    body: 'Test message',
    recipients: [new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890')],
    metadata: ['key' => 'value']
);

$array = $dto->toArray();
// Convert to JSON
$json = json_encode($dto->toArray());
```

### Validation

The DTOs include comprehensive validation:

- Message body cannot be empty
- Recipients array cannot be empty
- Metadata limited to 50 keys
- Metadata keys limited to 50 characters
- Metadata values limited to 500 characters

## Error Handling

The service automatically handles:

- Invalid phone numbers
- Empty message bodies
- API connection issues
- Rate limiting
- Validation errors with detailed messages

## What Happens Next

- **SMS Driver**: Message is sent to Notifyre API and returns response data
- **Log Driver**: Message is logged to Laravel logs (for testing) and returns mock response
