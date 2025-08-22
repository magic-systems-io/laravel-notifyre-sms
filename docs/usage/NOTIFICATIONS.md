# Laravel Notifications

Send SMS through Laravel's notification system for queuing, events, and more features.

## Quick Example

```php
<?php

namespace App\Notifications;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\Enums\NotifyreRecipientTypes;
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
            recipients: [new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, $notifiable->phone_number)]
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
        recipients: [new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, $notifiable->phone_number)]
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
            recipients: [new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, $notifiable->phone_number)],
            metadata: [
                'order_number' => $this->orderNumber,
                'total_amount' => (string) $this->total,
                'notification_type' => 'order_confirmation'
            ],
            campaignName: 'Order Confirmations'
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
            recipients: [new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, $notifiable->phone_number)],
            metadata: [
                'meeting_time' => $this->meetingTime,
                'location' => $this->location,
                'notification_type' => 'meeting_reminder'
            ]
        );
    }
}
```

### Scheduled Birthday Notification

```php
class BirthdayNotification extends Notification
{
    public function __construct(
        private string $name,
        private string $birthday
    ) {}

    public function via(object $notifiable): array
    {
        return ['notifyre'];
    }

    public function toNotifyre(): RequestBodyDTO
    {
        return new RequestBodyDTO(
            body: "Happy Birthday {$this->name}! 🎉",
            recipients: [new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, $notifiable->phone_number)],
            scheduledDate: strtotime($this->birthday . ' 9:00 AM'),
            addUnsubscribeLink: true,
            metadata: [
                'customer_name' => $this->name,
                'birthday_date' => $this->birthday,
                'notification_type' => 'birthday'
            ],
            campaignName: 'Birthday Campaign'
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
- **Advanced features**: Scheduling, metadata, callbacks, and campaign tracking

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

## Advanced Features

### Using Different Recipient Types

```php
public function toNotifyre(): RequestBodyDTO
{
    return new RequestBodyDTO(
        body: 'Welcome to our service!',
        recipients: [
            // Send to phone number
            new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, $notifiable->phone_number),
            // Or send to a contact in your Notifyre account
            new Recipient(NotifyreRecipientTypes::CONTACT->value, 'contact_123'),
            // Or send to a group
            new Recipient(NotifyreRecipientTypes::GROUP->value, 'group_456')
        ]
    );
}
```

### Adding Callbacks and Metadata

```php
public function toNotifyre(): RequestBodyDTO
{
    return new RequestBodyDTO(
        body: 'Your delivery is on its way!',
        recipients: [new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, $notifiable->phone_number)],
        callbackUrl: 'https://yourapp.com/sms-delivery-status',
        metadata: [
            'customer_id' => $notifiable->id,
            'delivery_date' => date('Y-m-d'),
            'notification_type' => 'delivery_update'
        ],
        campaignName: 'Delivery Updates'
    );
}
```
