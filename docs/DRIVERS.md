# Notifyre Laravel Package - Drivers

This document explains the different drivers available in the Notifyre package and how to use them effectively.

## Overview

The Notifyre package uses a driver-based architecture that allows you to switch between different SMS sending methods
without changing your application code. This is particularly useful for:

- **Development**: Use the log driver to avoid sending real SMS during development
- **Testing**: Test SMS functionality without incurring costs
- **Production**: Use the SMS driver to send real messages via Notifyre API

## Available Drivers

### 1. SMS Driver (`sms`)

The SMS driver is the production driver that sends real SMS messages via the Notifyre API.

#### Configuration

```env
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_TOKEN=your_actual_api_key
NOTIFYRE_BASE_URL=https://api.notifyre.com
```

#### Features

- **Real SMS Sending**: Actually sends SMS messages to recipients
- **API Integration**: Communicates with Notifyre's SMS API
- **Retry Logic**: Automatically retries failed requests
- **Error Handling**: Comprehensive error handling and logging
- **Response Caching**: Optional caching of API responses
- **Rate Limiting**: Built-in rate limiting to respect API limits

#### Use Cases

- Production environments
- Live applications
- Customer-facing SMS services
- Marketing campaigns
- Transactional notifications

#### Example Usage

```php
<?php

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;

// This will send a real SMS
notifyre()->send(new RequestBodyDTO(
    body: 'Your order has been shipped!',
    sender: 'ShopApp',
    recipients: [
        new Recipient('mobile_number', '+1234567890'),
    ]
));
```

### 2. Log Driver (`log`)

The log driver is the development/testing driver that logs SMS messages to Laravel logs instead of sending them.

#### Configuration

```env
NOTIFYRE_DRIVER=log
NOTIFYRE_API_TOKEN=any_value  # Not used, but required for consistency
```

#### Features

- **No External Calls**: No API requests are made
- **Fast Execution**: Immediate response for testing
- **Logging**: All SMS details are logged to Laravel logs
- **Cost-Free**: No SMS charges incurred
- **Easy Debugging**: See exactly what would be sent

#### Use Cases

- Development environments
- Testing and CI/CD pipelines
- Staging environments
- Debugging SMS functionality
- Local development

#### Example Usage

```php
<?php

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;

// This will log to Laravel logs instead of sending
notifyre()->send(new RequestBodyDTO(
    body: 'Test message (will be logged)',
    sender: 'TestApp',
    recipients: [
        new Recipient('mobile_number', '+1234567890'),
    ]
));

// Check your logs at storage/logs/laravel.log
```

## Driver Configuration

### Environment Variables

```env
# Required for all drivers
NOTIFYRE_DRIVER=sms  # or 'log'

# Required for SMS driver
NOTIFYRE_API_TOKEN=your_api_key

# Optional for SMS driver
NOTIFYRE_BASE_URL=https://api.notifyre.com
NOTIFYRE_TIMEOUT=30
NOTIFYRE_RETRY_TIMES=3
NOTIFYRE_RETRY_SLEEP=1000
```

### Configuration File

```php
// config/notifyre.php
return [
    'driver' => env('NOTIFYRE_DRIVER', NotifyreDriver::LOG),
    'api_key' => env('NOTIFYRE_API_KEY', ''),
    'base_url' => env('NOTIFYRE_BASE_URL', 'https://api.notifyre.com'),
    // ... other options
];
```

## Switching Between Drivers

### Development to Production

```bash
# Development (.env)
NOTIFYRE_DRIVER=log
NOTIFYRE_API_TOKEN=dev_token

# Production (.env)
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_TOKEN=production_token
```

### Using Configuration

```php
<?php

// In your application code
if (config('app.env') === 'production') {
    config(['notifyre.driver' => 'sms']);
} else {
    config(['notifyre.driver' => 'log']);
}
```

## Driver-Specific Features

### SMS Driver Features

#### Retry Logic

```php
// config/notifyre.php
'retry' => [
    'times' => env('NOTIFYRE_RETRY_TIMES', 3),
    'sleep' => env('NOTIFYRE_RETRY_SLEEP', 1000), // milliseconds
],
```

#### Rate Limiting

```php
// config/notifyre.php
'rate_limiting' => [
    'delay_between_sms' => env('NOTIFYRE_SMS_DELAY', 1), // seconds
    'max_per_minute' => env('NOTIFYRE_MAX_PER_MINUTE', 60),
],
```

#### Caching

```php
// config/notifyre.php
'cache' => [
    'enabled' => env('NOTIFYRE_CACHE_ENABLED', true),
    'ttl' => env('NOTIFYRE_CACHE_TTL', 3600), // seconds
    'prefix' => env('NOTIFYRE_CACHE_PREFIX', 'notifyre_'),
],
```

### Log Driver Features

#### Log Format

The log driver logs SMS messages in this format:

```php
logger('SMS would be sent via Notifyre', [
    'body' => 'Your message content',
    'sender' => 'SenderName',
    'recipients' => [
        [
            'type' => 'mobile_number',
            'value' => '+1234567890',
        ],
    ],
]);
```

#### Custom Logging

You can customize the logging behavior by extending the LogDriver:

```php
<?php

namespace App\Services;

use Arbi\Notifyre\Services\Drivers\LogDriver;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;

class CustomLogDriver extends LogDriver
{
    public function send(RequestBodyDTO $requestBody): void
    {
        // Custom logging logic
        logger('Custom SMS Log', [
            'message' => $requestBody->body,
            'timestamp' => now(),
            'recipients' => $requestBody->recipients,
        ]);
    }
}
```

## Testing with Drivers

### Unit Testing

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\DTO\SMS\Recipient;

class SMSDriverTest extends TestCase
{
    public function test_log_driver_logs_messages(): void
    {
        // Set log driver for testing
        config(['notifyre.driver' => 'log']);
        
        $message = new RequestBodyDTO(
            body: 'Test message',
            sender: 'TestApp',
            recipients: [
                new Recipient('mobile_number', '+1234567890'),
            ]
        );

        // This should log instead of send
        notifyre()->send($message);
        
        // Assert logging behavior
        $this->assertTrue(true); // Add your assertions
    }
}
```

### Feature Testing

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Notifications\TestNotification;

class SMSNotificationTest extends TestCase
{
    public function test_sms_notification_works_with_log_driver(): void
    {
        // Set log driver for testing
        config(['notifyre.driver' => 'log']);
        
        $user = User::factory()->create([
            'phone_number' => '+1234567890',
        ]);

        // This should log to Laravel logs
        $user->notify(new TestNotification());
        
        // Assert notification was processed
        $this->assertTrue(true); // Add your assertions
    }
}
```

## Best Practices

### Driver Selection

1. **Always use `log` driver in development** - Prevents accidental SMS sending
2. **Use `log` driver in CI/CD** - Ensures tests don't send real SMS
3. **Use `sms` driver only in production** - When you're ready to send real messages
4. **Test driver switching** - Ensure your app works with both drivers

### Configuration Management

1. **Environment-specific configs** - Use different .env files for different environments
2. **Validation** - Validate driver configuration before sending
3. **Fallbacks** - Have fallback mechanisms for failed SMS sends
4. **Monitoring** - Monitor SMS sending in production

### Testing Strategy

1. **Unit tests with log driver** - Test SMS logic without external dependencies
2. **Integration tests with log driver** - Test full notification flow
3. **Production tests** - Test with SMS driver in staging environment
4. **Mock external services** - Mock Notifyre API for comprehensive testing

## Troubleshooting

### Common Issues

#### Driver Not Found

```bash
# Error: Driver 'invalid_driver' not found
# Solution: Check NOTIFYRE_DRIVER in .env
NOTIFYRE_DRIVER=sms  # or 'log'
```

#### API Key Missing

```bash
# Error: Notifyre API key is not configured
# Solution: Set NOTIFYRE_API_TOKEN in .env
NOTIFYRE_API_TOKEN=your_actual_api_key
```

#### Base URL Issues

```bash
# Error: Notifyre base URL is not configured
# Solution: Set NOTIFYRE_BASE_URL in .env
NOTIFYRE_BASE_URL=https://api.notifyre.com
```

### Debug Mode

Enable debug logging to see what's happening:

```php
// In your application
if (config('app.debug')) {
    \Log::debug('SMS Driver', [
        'driver' => config('notifyre.driver'),
        'api_key_set' => !empty(config('notifyre.api_key')),
        'base_url' => config('notifyre.base_url'),
    ]);
}
```

## Next Steps

1. [Learn about configuration options](./CONFIGURATION.md)
2. [See usage examples](./USAGE.md)
3. [Explore practical examples](./EXAMPLES.md)
