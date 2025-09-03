# Direct SMS

Send SMS messages immediately using the `notifyre()` helper function with rich DTO support.

## Quick Example

```php
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;

notifyre()->send(new RequestBody(
    body: 'Hello World!',
    recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+1234567890')]
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

- **`body`** (required) - The SMS message text (max 160 characters)
- **`recipients`** (required) - Array of Recipient objects
- **`sender`** (optional) - The mobile phone number sending the SMS (empty for shared number)

### Recipient

- **`type`** - Currently supports `'mobile_number'`, `'contact'`, and `'group'`
- **`value`** - The phone number, contact ID, or group ID

## Examples

### Basic SMS

```php
notifyre()->send(new RequestBody(
    body: 'Your order has shipped!',
    recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+15551234567')]
));
```

### Multiple Recipients

```php
notifyre()->send(new RequestBody(
    body: 'Meeting reminder: 2 PM today',
    recipients: [
        new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+15551234567'),
        new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+15559876543'),
    ]
));
```

### SMS with Sender

```php
notifyre()->send(new RequestBody(
    body: 'Your delivery is on its way!',
    recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+1234567890')],
    sender: '+1987654321'
));
```

### Using Different Recipient Types

```php
// Send to a contact in your Notifyre account
notifyre()->send(new RequestBody(
    body: 'Welcome to our service!',
    recipients: [new Recipient(NotifyreRecipientTypes::CONTACT->value, 'contact_123')]
));

// Send to a group
notifyre()->send(new RequestBody(
    body: 'Group announcement: New features available!',
    recipients: [new Recipient(NotifyreRecipientTypes::GROUP->value, 'group_456')]
));
```

## Response Handling

The service returns a `ResponseBodyDTO` that you can use to track delivery:

```php
$response = notifyre()->send(new RequestBody(
    body: 'Test message',
    recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+1234567890')]
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
$dto = new RequestBody(
    body: 'Test message',
    recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+1234567890')]
);

$array = $dto->toArray();
// Convert to JSON
$json = json_encode($dto->toArray());
```

### Validation

The DTOs include comprehensive validation:

- Message body cannot be empty
- Recipients array cannot be empty
- Recipient type must be valid
- Recipient value cannot be empty

## Error Handling

The service automatically handles:

- Invalid phone numbers
- Empty message bodies
- Invalid recipient types
- API connection issues
- Validation errors with detailed messages

## What Happens Next

- **SMS Driver**: Message is sent to Notifyre API and returns response data
- **Log Driver**: Message is logged to Laravel logs (for testing) and returns null

## Configuration

Make sure you have the required environment variables set:

```env
NOTIFYRE_DRIVER=sms  # or 'log' for testing
NOTIFYRE_API_KEY=your_api_key_here
NOTIFYRE_BASE_URL=https://api.notifyre.com
```

## Next Steps

- Learn about [Laravel notifications](./NOTIFICATIONS.md)
- Explore [CLI commands](./COMMANDS.md)
- Check out [REST API usage](./API.md)
- Review [Configuration options](../getting-started/CONFIGURATION.md)
