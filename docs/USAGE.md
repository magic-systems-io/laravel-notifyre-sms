# Notifyre Laravel Package - Usage Guide

This guide shows you how to use the Notifyre Laravel package to send SMS notifications through various methods.

## Quick Start

The package provides two main ways to send SMS:

1. **Direct SMS** - Fast, simple sending (like Vonage)
2. **Laravel Notifications** - Full notification system features

## Method 1: Direct SMS (Helper Function)

The simplest way to send SMS messages directly:

```php
<?php

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;

// Send a simple SMS directly
notifyre()->send(new RequestBodyDTO(
    body: 'Hello! This is a test message from Notifyre.',
    sender: 'MyApp', // Optional: will use default if not provided
    recipients: [
        new Recipient('mobile_number', '+1234567890'),
        new Recipient('mobile_number', '+0987654321'),
    ]
));
```

### Recipient Types

The package currently supports one recipient type:

1. **`mobile_number`** - Direct phone number (e.g., '+1234567890')

## Method 2: Direct SMS (Facade)

```php
<?php

use Arbi\Notifyre\Facades\Notifyre;
use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;

// Send SMS directly via facade
Notifyre::send(new RequestBodyDTO(
    body: 'Your order #12345 has been shipped!',
    sender: 'ShopApp',
    recipients: [
        new Recipient('mobile_number', '+15551234567'),
    ]
));
```

## Method 3: Laravel Notifications (Recommended)

### Create a Notification Class

```php
<?php

namespace App\Notifications;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderShippedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private string $orderNumber,
        private string $trackingNumber
    ) {}

    public function via(object $notifiable): array
    {
        return ['notifyre'];
    }

    public function toNotifyre(): RequestBodyDTO
    {
        return new RequestBodyDTO(
            body: "Your order #{$this->orderNumber} has been shipped! Track it at: {$this->trackingNumber}",
            sender: 'ShopApp',
            recipients: [
                new Recipient('mobile_number', $notifiable->phone_number),
            ]
        );
    }
}
```

### Create a Notifiable Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
    ];

    /**
     * Route notifications for the Notifyre channel.
     * This method is called by Laravel to determine how to route notifications.
     */
    public function routeNotificationForNotifyre(): string
    {
        return $this->phone_number;
    }
}
```

### Send the Notification

```php
<?php

use App\Models\User;
use App\Notifications\OrderShippedNotification;

// Send to a single user
$user = User::find(1);
$user->notify(new OrderShippedNotification('12345', 'TRK789'));

// Send to multiple users
$users = User::where('has_orders', true)->get();
foreach ($users as $user) {
    $user->notify(new OrderShippedNotification('12345', 'TRK789'));
}

// Send immediately (bypass queue)
$user->notifyNow(new OrderShippedNotification('12345', 'TRK789'));
```

## Method 4: Using Notifiable Classes

### Simple Notifiable Class

```php
<?php

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Illuminate\Notifications\Notifiable;

class SMSRecipient
{
    use Notifiable;

    public function __construct(
        private string $phoneNumber,
        private string $name
    ) {}

    public function getKey(): string
    {
        return $this->phoneNumber;
    }

    public function routeNotificationForNotifyre(): string
    {
        return $this->phoneNumber;
    }
}

// Usage
$recipient = new SMSRecipient('+1234567890', 'John Doe');
$recipient->notify(new class extends \Illuminate\Notifications\Notification {
    public function via(object $notifiable): array
    {
        return ['notifyre'];
    }

    public function toNotifyre(): RequestBodyDTO
    {
        return new RequestBodyDTO(
            body: "Hello {$notifiable->name}! Welcome to our service.",
            sender: 'WelcomeApp',
            recipients: [
                new Recipient('mobile_number', $notifiable->phoneNumber),
            ]
        );
    }
});
```

## Method 5: CLI Command

Send SMS directly from the command line:

```bash
# Send SMS with all parameters
php artisan sms:send "SenderName" "+1234567890" "Hello World!"

# Use defaults from config
php artisan sms:send "" "" "Test message"
```

## Advanced Usage Examples

### Bulk SMS with Different Messages

```php
<?php

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;

$recipients = [
    ['phone' => '+1234567890', 'name' => 'John'],
    ['phone' => '+0987654321', 'name' => 'Jane'],
    ['phone' => '+1122334455', 'name' => 'Bob'],
];

foreach ($recipients as $recipient) {
    notifyre()->send(new RequestBodyDTO(
        body: "Hello {$recipient['name']}! Your account has been updated.",
        sender: 'AccountApp',
        recipients: [
            new Recipient('mobile_number', $recipient['phone']),
        ]
    ));
}
```

### Conditional SMS Based on User Preferences

```php
<?php

use App\Models\User;
use App\Notifications\MarketingNotification;

class MarketingService
{
    public function sendMarketingSMS(): void
    {
        $users = User::where('sms_notifications_enabled', true)
            ->where('marketing_consent', true)
            ->get();

        foreach ($users as $user) {
            if ($user->shouldReceiveMarketingSMS()) {
                $user->notify(new MarketingNotification());
            }
        }
    }
}
```

### Error Handling

```php
<?php

use Arbi\Notifyre\Exceptions\ApiException;
use Arbi\Notifyre\Exceptions\InvalidConfigurationException;

try {
    notifyre()->send(new RequestBodyDTO(
        body: 'Test message',
        sender: 'TestApp',
        recipients: [
            new Recipient('mobile_number', '+1234567890'),
        ]
    ));
} catch (InvalidConfigurationException $e) {
    // Handle configuration errors
    logger('Notifyre configuration error: ' . $e->getMessage());
} catch (ApiException $e) {
    // Handle API errors
    logger('Notifyre API error: ' . $e->getMessage());
} catch (\Exception $e) {
    // Handle other errors
    logger('Unexpected error: ' . $e->getMessage());
}
```

## Testing

When using the `log` driver, SMS messages will be logged instead of sent:

```php
// In your .env for testing
NOTIFYRE_DRIVER=log

// Messages will appear in your Laravel logs
// Check storage/logs/laravel.log
```

## Queue Support

Notifications can be queued for better performance:

```php
<?php

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueuedSMSNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['notifyre'];
    }

    public function toNotifyre(): RequestBodyDTO
    {
        // Your SMS logic here
    }
}
```

## Driver-Specific Usage

### SMS Driver (Production)

- Sends real SMS via Notifyre API
- Includes retry logic and error handling
- Supports response caching
- Rate limiting enabled

### Log Driver (Development/Testing)

- Logs SMS to Laravel logs
- No external API calls
- Fast execution for testing
- No costs incurred

## Best Practices

1. **Use Laravel Notifications** for most cases - they provide queuing, events, and better integration
2. **Use direct sending** only for simple, one-off SMS messages
3. **Always validate phone numbers** before sending
4. **Use queues** for bulk SMS to avoid timeouts
5. **Handle errors gracefully** with proper exception handling
6. **Test with the log driver** in development
7. **Use meaningful sender names** for better user experience
8. **Implement rate limiting** for bulk SMS campaigns
9. **Monitor delivery status** and handle failures appropriately

## Key Differences

- **`notifyre()->send()`** - Direct SMS sending, bypasses Laravel notifications
- **`$user->notify()`** - Full Laravel notification system with all features

## Next Steps

1. [Learn about drivers](./DRIVERS.md)
2. [See practical examples](./EXAMPLES.md)
3. [Learn about configuration options](./CONFIGURATION.md)
