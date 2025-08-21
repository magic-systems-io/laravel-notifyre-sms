# Laravel Notifications

Send SMS through Laravel's notification system for queuing, events, and more features.

## Quick Example

```php
<?php

namespace App\Notifications;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['notifyre'];
    }

    public function toNotifyre(): RequestBodyDTO
    {
        return new RequestBodyDTO(
            body: 'Welcome to our app!',
            sender: 'MyApp',
            recipients: [new Recipient('mobile_number', $notifiable->phone_number)]
        );
    }
}

// Send the notification
$user->notify(new WelcomeNotification());
```

## How It Works

1. Create a notification class that extends `Illuminate\Notifications\Notification`
2. Return `['notifyre']` in the `via()` method
3. Implement `toNotifyre()` method that returns a `RequestBodyDTO`
4. Use Laravel's notification methods to send

## Required Methods

### `via()`

Return the channels this notification should be sent through:

```php
public function via(object $notifiable): array
{
    return ['notifyre'];
}
```

### `toNotifyre()`

Return the SMS message data:

```php
public function toNotifyre(): RequestBodyDTO
{
    return new RequestBodyDTO(
        body: 'Your message here',
        sender: 'AppName',
        recipients: [new Recipient('mobile_number', $notifiable->phone_number)]
    );
}
```

## Examples

### Order Confirmation

```php
class OrderConfirmationNotification extends Notification
{
    public function __construct(
        private string $orderNumber,
        private float $total
    ) {}

    public function via(object $notifiable): array
    {
        return ['notifyre'];
    }

    public function toNotifyre(): RequestBodyDTO
    {
        return new RequestBodyDTO(
            body: "Order #{$this->orderNumber} confirmed! Total: \${$this->total}",
            sender: 'ShopApp',
            recipients: [new Recipient('mobile_number', $notifiable->phone_number)]
        );
    }
}

// Send
$user->notify(new OrderConfirmationNotification('12345', 99.99));
```

### Meeting Reminder

```php
class MeetingReminderNotification extends Notification
{
    public function __construct(
        private string $meetingTime,
        private string $location
    ) {}

    public function via(object $notifiable): array
    {
        return ['notifyre'];
    }

    public function toNotifyre(): RequestBodyDTO
    {
        return new RequestBodyDTO(
            body: "Meeting reminder: {$this->meetingTime} at {$this->location}",
            sender: 'WorkApp',
            recipients: [new Recipient('mobile_number', $notifiable->phone_number)]
        );
    }
}
```

## Notifiable Models

Your model needs a `phone_number` attribute or method:

```php
class User extends Authenticatable
{
    public function routeNotificationForNotifyre(): string
    {
        return $this->phone_number;
    }
}
```

## Benefits of Notifications

- **Queuing**: Use `ShouldQueue` trait for background processing
- **Events**: Hook into notification lifecycle
- **Testing**: Easy to mock and test
- **Multiple channels**: Send to SMS, email, database, etc.
- **Rate limiting**: Built-in Laravel rate limiting

## Queue Support

```php
use Illuminate\Bus\Queueable;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['notifyre'];
    }

    public function toNotifyre(): RequestBodyDTO
    {
        // ... implementation
    }
}
```
