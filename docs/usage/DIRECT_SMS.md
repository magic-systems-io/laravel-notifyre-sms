# Direct SMS

Send SMS messages immediately using the `notifyre()` helper function.

## Quick Example

```php
use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;

notifyre()->send(new RequestBodyDTO(
    body: 'Hello World!',
    sender: 'MyApp',
    recipients: [new Recipient('mobile_number', '+1234567890')]
));
```

## How It Works

The `notifyre()` helper function returns the NotifyreService, which:
1. Creates the appropriate driver (SMS or Log)
2. Sends your message through that driver
3. Handles any errors automatically

## Parameters

### RequestBodyDTO

- **`body`** (required) - The SMS message text
- **`sender`** (optional) - Who the message is from (uses config default if not provided)
- **`recipients`** (required) - Array of Recipient objects

### Recipient

- **`type`** - Currently only supports `'mobile_number'`
- **`value`** - The phone number (e.g., `'+1234567890'`)

## Examples

### Single Recipient

```php
notifyre()->send(new RequestBodyDTO(
    body: 'Your order has shipped!',
    sender: 'ShopApp',
    recipients: [new Recipient('mobile_number', '+15551234567')]
));
```

### Multiple Recipients

```php
notifyre()->send(new RequestBodyDTO(
    body: 'Meeting reminder: 2 PM today',
    sender: 'WorkApp',
    recipients: [
        new Recipient('mobile_number', '+15551234567'),
        new Recipient('mobile_number', '+15559876543'),
    ]
));
```

### Using Default Sender

```php
notifyre()->send(new RequestBodyDTO(
    body: 'Test message',
    sender: null, // Will use config default
    recipients: [new Recipient('mobile_number', '+1234567890')]
));
```

## Error Handling

The service automatically handles:
- Invalid phone numbers
- Empty message bodies
- API connection issues
- Rate limiting

## What Happens Next

- **SMS Driver**: Message is sent to Notifyre API
- **Log Driver**: Message is logged to Laravel logs (for testing)
