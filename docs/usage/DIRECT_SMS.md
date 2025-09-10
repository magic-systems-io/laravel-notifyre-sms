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
3. Persists the message to database if enabled
4. Handles any errors automatically

## Parameters

### RequestBody

- **`body`** (required) - The SMS message text
- **`recipients`** (required) - Array of Recipient objects
- **`sender`** (optional) - The mobile phone number sending the SMS
- **`scheduledDate`** (optional) - Unix timestamp for scheduled sending
- **`addUnsubscribeLink`** (optional) - Add unsubscribe link to message
- **`callbackUrl`** (optional) - URL for delivery status callbacks
- **`metadata`** (optional) - Additional metadata array
- **`campaignName`** (optional) - Name for the SMS campaign

### Recipient

- **`type`** - Supports `'mobile_number'`, `'contact'`, and `'group'`
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

### Scheduled SMS

```php
notifyre()->send(new RequestBody(
    body: 'Reminder: Your appointment is tomorrow at 2 PM',
    recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+1234567890')],
    scheduledDate: time() + 3600 // Send in 1 hour
));
```

### SMS with Additional Options

```php
notifyre()->send(new RequestBody(
    body: 'Welcome to our service! Reply STOP to unsubscribe.',
    recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+1234567890')],
    sender: '+1987654321',
    addUnsubscribeLink: true,
    callbackUrl: 'https://yourapp.com/sms/callback',
    metadata: ['campaign' => 'welcome', 'user_id' => 123],
    campaignName: 'Welcome Campaign'
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

The service handles responses automatically:

```php
// For SMS driver: Sends message and persists to database
// For log driver: Logs message to Laravel logs
notifyre()->send(new RequestBody(
    body: 'Test message',
    recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+1234567890')]
));

// Check Laravel logs for log driver
// Check database for SMS driver
```

## Error Handling

The service throws exceptions for various error conditions:

```php
try {
    notifyre()->send(new RequestBody(
        body: 'Test message',
        recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+1234567890')]
    ));
} catch (InvalidArgumentException $e) {
    // Configuration errors (missing API key, invalid driver, etc.)
    echo "Configuration error: " . $e->getMessage();
} catch (ConnectionException $e) {
    // Network errors (timeout, connection failed, etc.)
    echo "Network error: " . $e->getMessage();
} catch (Exception $e) {
    // Other errors
    echo "Error: " . $e->getMessage();
}
```

## Configuration

Make sure you have the required environment variables set:

```env
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_api_key_here
NOTIFYRE_BASE_URL=https://api.notifyre.com
```

For testing, use the log driver:

```env
NOTIFYRE_DRIVER=log
```

## Database Persistence

When database persistence is enabled, SMS messages and recipients are automatically stored:

```php
// This will store the message in the database if NOTIFYRE_DB_ENABLED=true
notifyre()->send(new RequestBody(
    body: 'Stored message',
    recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+1234567890')]
));

// You can then retrieve the message from database
$message = NotifyreSmsMessages::latest()->first();
```

## Performance Tips

- Use the log driver for testing to avoid API calls
- Enable database persistence for message tracking
- Set appropriate timeouts in configuration
- Configure retry logic in configuration
