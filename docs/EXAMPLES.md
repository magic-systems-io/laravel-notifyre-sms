# Notifyre Laravel Package - Practical Examples

This file contains practical examples of how to use the Notifyre package in real-world scenarios.

## E-commerce Examples

### Order Confirmation

```php
<?php

namespace App\Notifications;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderConfirmationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private string $orderNumber,
        private float $total,
        private string $estimatedDelivery
    ) {}

    public function via(object $notifiable): array
    {
        return ['notifyre'];
    }

    public function toNotifyre(): RequestBodyDTO
    {
        return new RequestBodyDTO(
            body: "Order #{$this->orderNumber} confirmed! Total: \${$this->total}. Estimated delivery: {$this->estimatedDelivery}",
            sender: 'ShopApp',
            recipients: [
                new Recipient('mobile_number', $notifiable->phone_number),
            ]
        );
    }
}

// Usage
$user->notify(new OrderConfirmationNotification('12345', 99.99, '3-5 business days'));
```

### Shipping Update

```php
<?php

namespace App\Notifications;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Illuminate\Notifications\Notification;

class ShippingUpdateNotification extends Notification
{
    public function __construct(
        private string $orderNumber,
        private string $trackingNumber,
        private string $carrier
    ) {}

    public function via(object $notifiable): array
    {
        return ['notifyre'];
    }

    public function toNotifyre(): RequestBodyDTO
    {
        return new RequestBodyDTO(
            body: "Order #{$this->orderNumber} shipped via {$this->carrier}! Track: {$this->trackingNumber}",
            sender: 'ShopApp',
            recipients: [
                new Recipient('mobile_number', $notifiable->phone_number),
            ]
        );
    }
}
```

## User Management Examples

### Welcome Message

```php
<?php

namespace App\Notifications;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    public function __construct(
        private string $userName
    ) {}

    public function via(object $notifiable): array
    {
        return ['notifyre'];
    }

    public function toNotifyre(): RequestBodyDTO
    {
        return new RequestBodyDTO(
            body: "Welcome {$this->userName}! Your account has been created successfully.",
            sender: 'WelcomeApp',
            recipients: [
                new Recipient('mobile_number', $notifiable->phone_number),
            ]
        );
    }
}
```

### Password Reset

```php
<?php

namespace App\Notifications;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    public function __construct(
        private string $resetCode
    ) {}

    public function via(object $notifiable): array
    {
        return ['notifyre'];
    }

    public function toNotifyre(): RequestBodyDTO
    {
        return new RequestBodyDTO(
            body: "Your password reset code is: {$this->resetCode}. Valid for 10 minutes.",
            sender: 'SecurityApp',
            recipients: [
                new Recipient('mobile_number', $notifiable->phone_number),
            ]
        );
    }
}
```

## Marketing Examples

### Promotional Campaign

```php
<?php

namespace App\Notifications;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Illuminate\Notifications\Notification;

class PromotionalNotification extends Notification
{
    public function __construct(
        private string $discountCode,
        private int $discountPercent,
        private string $expiryDate
    ) {}

    public function via(object $notifiable): array
    {
        return ['notifyre'];
    }

    public function toNotifyre(): RequestBodyDTO
    {
        return new RequestBodyDTO(
            body: "Special offer! Use code {$this->discountCode} for {$this->discountPercent}% off. Expires {$this->expiryDate}",
            sender: 'DealsApp',
            recipients: [
                new Recipient('mobile_number', $notifiable->phone_number),
            ]
        );
    }
}
```

### Event Reminder

```php
<?php

namespace App\Notifications;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Illuminate\Notifications\Notification;

class EventReminderNotification extends Notification
{
    public function __construct(
        private string $eventName,
        private string $eventDate,
        private string $eventTime
    ) {}

    public function via(object $notifiable): array
    {
        return ['notifyre'];
    }

    public function toNotifyre(): RequestBodyDTO
    {
        return new RequestBodyDTO(
            body: "Reminder: {$this->eventName} is tomorrow at {$this->eventTime}. See you there!",
            sender: 'EventsApp',
            recipients: [
                new Recipient('mobile_number', $notifiable->phone_number),
            ]
        );
    }
}
```

## Service Examples

### Appointment Confirmation

```php
<?php

namespace App\Notifications;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Illuminate\Notifications\Notification;

class AppointmentConfirmationNotification extends Notification
{
    public function __construct(
        private string $serviceName,
        private string $appointmentDate,
        private string $appointmentTime,
        private string $providerName
    ) {}

    public function via(object $notifiable): array
    {
        return ['notifyre'];
    }

    public function toNotifyre(): RequestBodyDTO
    {
        return new RequestBodyDTO(
            body: "Appointment confirmed: {$this->serviceName} with {$this->providerName} on {$this->appointmentDate} at {$this->appointmentTime}",
            sender: 'BookingApp',
            recipients: [
                new Recipient('mobile_number', $notifiable->phone_number),
            ]
        );
    }
}
```

### Service Update

```php
<?php

namespace App\Notifications;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Illuminate\Notifications\Notification;

class ServiceUpdateNotification extends Notification
{
    public function __construct(
        private string $serviceName,
        private string $status,
        private ?string $estimatedCompletion = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['notifyre'];
    }

    public function toNotifyre(): RequestBodyDTO
    {
        $message = "Your {$this->serviceName} is now {$this->status}";
        
        if ($this->estimatedCompletion) {
            $message .= ". Estimated completion: {$this->estimatedCompletion}";
        }

        return new RequestBodyDTO(
            body: $message,
            sender: 'ServiceApp',
            recipients: [
                new Recipient('mobile_number', $notifiable->phone_number),
            ]
        );
    }
}
```

## Bulk SMS Examples

### Mass Notification

```php
<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SystemMaintenanceNotification;
use Illuminate\Support\Facades\Log;

class BulkNotificationService
{
    public function sendMaintenanceNotification(string $message, string $scheduledTime): void
    {
        $users = User::where('sms_notifications_enabled', true)
            ->where('role', '!=', 'guest')
            ->get();

        $successCount = 0;
        $failureCount = 0;

        foreach ($users as $user) {
            try {
                $user->notify(new SystemMaintenanceNotification($message, $scheduledTime));
                $successCount++;
            } catch (\Exception $e) {
                Log::error("Failed to send SMS to user {$user->id}: " . $e->getMessage());
                $failureCount++;
            }
        }

        Log::info("Bulk SMS completed: {$successCount} successful, {$failureCount} failed");
    }
}
```

### Conditional Bulk SMS

```php
<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\PersonalizedOfferNotification;

class PersonalizedMarketingService
{
    public function sendPersonalizedOffers(): void
    {
        $users = User::where('sms_notifications_enabled', true)
            ->where('marketing_consent', true)
            ->with('purchaseHistory')
            ->get();

        foreach ($users as $user) {
            $offer = $this->generatePersonalizedOffer($user);
            
            if ($offer) {
                $user->notify(new PersonalizedOfferNotification($offer));
            }
        }
    }

    private function generatePersonalizedOffer(User $user): ?string
    {
        // Your offer generation logic here
        $totalSpent = $user->purchaseHistory->sum('amount');
        
        if ($totalSpent > 1000) {
            return "VIP offer: 20% off your next purchase!";
        } elseif ($totalSpent > 500) {
            return "Special offer: 15% off your next purchase!";
        } elseif ($totalSpent > 100) {
            return "Limited time: 10% off your next purchase!";
        }

        return null;
    }
}
```

## Error Handling Examples

### Graceful Fallback

```php
<?php

namespace App\Services;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\Exceptions\ApiException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SMSFallbackService
{
    public function sendWithFallback(RequestBodyDTO $message, object $notifiable): void
    {
        try {
            // Try SMS first
            notifyre()->send($message);
            Log::info("SMS sent successfully to {$notifiable->phone_number}");
        } catch (ApiException $e) {
            Log::warning("SMS failed, falling back to email: " . $e->getMessage());
            
            // Fallback to email
            $this->sendEmailFallback($message, $notifiable);
        } catch (\Exception $e) {
            Log::error("Both SMS and email failed: " . $e->getMessage());
            throw $e;
        }
    }

    private function sendEmailFallback(RequestBodyDTO $message, object $notifiable): void
    {
        // Send email with SMS content
        Mail::to($notifiable->email)->send(new SMSFallbackMail($message->body));
    }
}
```

### Retry Logic

```php
<?php

namespace App\Services;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\Exceptions\ApiException;
use Illuminate\Support\Facades\Log;

class SMSRetryService
{
    private const MAX_RETRIES = 3;
    private const RETRY_DELAY = 1000; // milliseconds

    public function sendWithRetry(RequestBodyDTO $message): void
    {
        $attempts = 0;
        
        while ($attempts < self::MAX_RETRIES) {
            try {
                notifyre()->send($message);
                Log::info("SMS sent successfully on attempt " . ($attempts + 1));
                return;
            } catch (ApiException $e) {
                $attempts++;
                
                if ($attempts >= self::MAX_RETRIES) {
                    Log::error("SMS failed after {$attempts} attempts: " . $e->getMessage());
                    throw $e;
                }
                
                Log::warning("SMS attempt {$attempts} failed, retrying in " . (self::RETRY_DELAY / 1000) . " seconds");
                usleep(self::RETRY_DELAY * 1000);
            }
        }
    }
}
```

## Testing Examples

### Mock SMS for Testing

```php
<?php

namespace Tests\Unit;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\Services\NotifyreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SMSServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sms_sending_logs_message_when_log_driver_enabled(): void
    {
        // Set log driver for testing
        config(['notifyre.driver' => 'log']);
        
        $service = app(NotifyreService::class);
        $message = new RequestBodyDTO(
            body: 'Test message',
            sender: 'TestApp',
            recipients: [
                new Recipient('mobile_number', '+1234567890'),
            ]
        );

        // This should log to Laravel logs instead of sending
        $service->send($message);
        
        // Assert that the message was logged
        $this->assertTrue(true); // Add your logging assertions here
    }
}
```

### Notification Testing

```php
<?php

namespace Tests\Unit;

use App\Models\User;
use App\Notifications\OrderConfirmationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OrderNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_confirmation_notification_sends_sms(): void
    {
        Notification::fake();
        
        $user = User::factory()->create([
            'phone_number' => '+1234567890',
        ]);

        $user->notify(new OrderConfirmationNotification('12345', 99.99, '3-5 days'));
        
        Notification::assertSentTo(
            $user,
            OrderConfirmationNotification::class,
            function ($notification) {
                return $notification->toNotifyre()->body === 'Order #12345 confirmed! Total: $99.99. Estimated delivery: 3-5 days';
            }
        );
    }
}
```

## Performance Examples

### Queue for Bulk SMS

```php
<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\BulkMarketingNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBulkSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private array $userIds,
        private string $message
    ) {}

    public function handle(): void
    {
        $users = User::whereIn('id', $this->userIds)
            ->where('sms_notifications_enabled', true)
            ->get();

        foreach ($users as $user) {
            $user->notify(new BulkMarketingNotification($this->message));
            
            // Rate limiting
            usleep(100000); // 0.1 second delay between sends
        }
    }
}

// Usage
SendBulkSMSJob::dispatch($userIds, 'Special offer today only!')
    ->onQueue('sms')
    ->delay(now()->addMinutes(5));
```

### Batch Processing

```php
<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\BatchNotification;
use Illuminate\Support\Collection;

class BatchSMSService
{
    private const BATCH_SIZE = 100;

    public function sendBatchNotification(string $message): void
    {
        User::where('sms_notifications_enabled', true)
            ->chunk(self::BATCH_SIZE, function (Collection $users) use ($message) {
                foreach ($users as $user) {
                    $user->notify(new BatchNotification($message));
                }
                
                // Small delay between batches to avoid overwhelming the API
                usleep(500000); // 0.5 seconds
            });
    }
}
```

## Driver-Specific Examples

### SMS Driver Usage

```php
<?php

namespace App\Services;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;

class ProductionSMSService
{
    public function sendProductionSMS(): void
    {
        // Ensure SMS driver is configured
        if (config('notifyre.driver') !== 'sms') {
            throw new \Exception('SMS driver not configured for production');
        }

        $message = new RequestBodyDTO(
            body: 'Production SMS message',
            sender: 'ProdApp',
            recipients: [
                new Recipient('mobile_number', '+1234567890'),
            ]
        );

        notifyre()->send($message);
    }
}
```

### Log Driver Usage

```php
<?php

namespace App\Services;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;

class DevelopmentSMSService
{
    public function sendTestSMS(): void
    {
        // Ensure log driver is configured for testing
        if (config('notifyre.driver') !== 'log') {
            throw new \Exception('Log driver not configured for testing');
        }

        $message = new RequestBodyDTO(
            body: 'Test SMS message (will be logged)',
            sender: 'TestApp',
            recipients: [
                new Recipient('mobile_number', '+1234567890'),
            ]
        );

        // This will log to Laravel logs instead of sending
        notifyre()->send($message);
    }
}
```

These examples demonstrate various real-world use cases and best practices for using the Notifyre package effectively in your Laravel applications.

## Next Steps

1. [Learn about drivers](./DRIVERS.md)
2. [See usage examples](./USAGE.md)
3. [Learn about configuration options](./CONFIGURATION.md)
