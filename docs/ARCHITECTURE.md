# Notifyre Laravel Package - Architecture & Internals

This guide explains the internal architecture of the Notifyre package, how components interact, and how to extend or customize the package.

## Package Architecture Overview

The Notifyre package follows clean architecture principles with clear separation of concerns:

```
┌─────────────────────────────────────────────────────────────────┐
│                    Application Layer                            │
├─────────────────────────────────────────────────────────────────┤
│  Commands  │  Facades  │  Helpers  │  Notifications  │  HTTP  │
├─────────────────────────────────────────────────────────────────┤
│                    Service Layer                               │
├─────────────────────────────────────────────────────────────────┤
│  NotifyreService  │  DriverFactory  │  NotifyreChannel        │
├─────────────────────────────────────────────────────────────────┤
│                    Driver Layer                                │
├─────────────────────────────────────────────────────────────────┤
│  SMSDriver  │  LogDriver  │  Custom Drivers                   │
├─────────────────────────────────────────────────────────────────┤
│                    Data Layer                                  │
├─────────────────────────────────────────────────────────────────┤
│  DTOs  │  Enums  │  Contracts  │  Exceptions                 │
└─────────────────────────────────────────────────────────────────┘
```

## Core Components

### 1. Service Provider (`NotifyreServiceProvider`)

The service provider is the heart of the package, responsible for:

- **Service Registration**: Binding interfaces to concrete implementations
- **Configuration Publishing**: Making config files available to applications
- **Command Registration**: Registering Artisan commands
- **Channel Extension**: Extending Laravel's notification system

#### Registration Process

```php
public function register(): void
{
    // Merge default configuration
    if (method_exists($this, 'mergeConfigFrom') && function_exists('config_path')) {
        $this->mergeConfigFrom(self::CONFIG_PATH, 'notifyre');
    }

    // Register service bindings
    foreach (self::SINGLETONS as $abstract => $concrete) {
        $this->app->singleton($abstract, function ($app) use ($concrete) {
            return match ($concrete) {
                NotifyreService::class => new NotifyreService($app->make(NotifyreDriverFactoryInterface::class)),
                'notifyre' => $app->make('notifyre'),
                DriverFactory::class => new DriverFactory(),
            };
        });
    }
}
```

#### Service Bindings

The package registers these services as singletons:

```php
private const array SINGLETONS = [
    'notifyre' => NotifyreService::class,                    // Main service alias
    NotifyreServiceInterface::class => 'notifyre',            // Interface binding
    DriverFactory::class => DriverFactory::class,             // Driver factory
    NotifyreDriverFactoryInterface::class => DriverFactory::class, // Factory interface
];
```

#### Boot Process

```php
public function boot(): void
{
    $this->publishConfig();           // Publish configuration files
    $this->registerCommands();        // Register Artisan commands
    $this->extendNotificationChannel(); // Extend notification system
}
```

### 2. Core Services

#### NotifyreService

The main service that orchestrates SMS sending:

```php
readonly class NotifyreService implements NotifyreServiceInterface
{
    public function __construct(private NotifyreDriverFactoryInterface $driverFactory)
    {
    }

    public function send(RequestBodyDTO $message): void
    {
        $driver = $this->driverFactory->create();
        $driver->send($message);
    }
}
```

**Responsibilities:**
- Delegates SMS sending to appropriate driver
- Maintains single responsibility principle
- Readonly for immutability

#### DriverFactory

Creates and manages driver instances:

```php
class DriverFactory implements NotifyreDriverFactoryInterface
{
    public function create(): NotifyreDriverInterface
    {
        $driver = $this->getConfiguredDriver();
        
        return match ($driver) {
            NotifyreDriver::SMS => new SMSDriver(),
            NotifyreDriver::LOG => new LogDriver(),
            default => throw new InvalidArgumentException("Invalid Notifyre driver '{$driver}'...")
        };
    }
}
```

**Features:**
- Configuration-based driver selection
- Priority system for driver configuration
- Validation of driver values
- Easy extension for custom drivers

### 3. Driver System

#### Driver Interface

All drivers implement the `NotifyreDriverInterface`:

```php
interface NotifyreDriverInterface
{
    /**
     * Send SMS message
     * @throws InvalidArgumentException
     */
    public function send(RequestBodyDTO $requestBody): void;
}
```

#### Available Drivers

1. **SMSDriver**: Production driver for real SMS sending
2. **LogDriver**: Development driver for logging SMS

#### Driver Selection Priority

```php
private function getConfiguredDriver(): string
{
    // Priority 1: services.notifyre.driver
    $driver = config('services.notifyre.driver');
    
    if (!empty($driver)) {
        return $driver;
    }
    
    // Priority 2: notifyre.driver
    $driver = config('notifyre.driver');
    
    if (empty($driver)) {
        throw new InvalidArgumentException('Notifyre driver is not configured.');
    }
    
    return $driver;
}
```

### 4. Data Transfer Objects (DTOs)

#### RequestBodyDTO

Represents an SMS request:

```php
readonly class RequestBodyDTO
{
    public function __construct(
        public string $body,
        public ?string $sender,
        public array $recipients
    ) {
        $this->validate();
    }
}
```

**Validation Rules:**
- Body cannot be empty or whitespace-only
- Recipients array cannot be empty
- Each recipient must be a valid Recipient instance

#### Recipient

Represents an SMS recipient:

```php
readonly class Recipient
{
    public const VALID_TYPES = ['mobile_number', 'contact', 'group'];
    
    public function __construct(
        public string $type,
        public string $value
    ) {
        $this->validate();
    }
}
```

**Supported Types:**
- `mobile_number`: Direct phone numbers
- `contact`: Contact identifiers
- `group`: Group identifiers

### 5. Notification Channel

#### NotifyreChannel

Extends Laravel's notification system:

```php
readonly class NotifyreChannel implements ChannelInterface
{
    public function __construct(private NotifyreDriverFactoryInterface $driverFactory)
    {
    }

    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toNotifyre')) {
            throw new InvalidArgumentException('Notification does not have a toNotifyre method.');
        }

        $message = $notification->toNotifyre($notifiable);
        $driver = $this->driverFactory->create();
        $driver->send($message);
    }
}
```

**Features:**
- Automatic driver selection
- Method validation
- Error handling
- Integration with Laravel notifications

### 6. HTTP Components (Placeholder)

The package includes HTTP components for potential future API endpoints:

#### NotifyreController

Currently a placeholder controller with standard REST methods:

```php
class NotifyreController
{
    public function index(Request $request): JsonResponse
    public function store(Request $request): JsonResponse
    public function show(Request $request): JsonResponse
    public function update(Request $request): JsonResponse
    public function destroy(Request $request): JsonResponse
}
```

**Note**: This controller is currently a placeholder and does not implement actual SMS functionality. It's included for future API endpoint development.

#### NotifyreRequest

Form request validation for SMS data:

```php
class NotifyreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:160'],
            'sender' => ['nullable', 'string', 'max:255'],
            'recipients' => ['required', 'array'],
            'recipients.*' => ['required', 'string', 'max:255'],
        ];
    }
}
```

#### API Routes

The package registers API routes automatically:

```php
Route::apiResource('notifyre', NotifyreController::class);
```

**Current Status**: These components are included for future development but are not actively used for SMS functionality. The package currently focuses on programmatic SMS sending through services and notifications.

## Extension Points

### 1. Custom Drivers

Create custom drivers by implementing the interface:

```php
<?php

namespace App\Services\Drivers;

use Arbi\Notifyre\Contracts\NotifyreDriverInterface;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;

class CustomDriver implements NotifyreDriverInterface
{
    public function send(RequestBodyDTO $requestBody): void
    {
        // Custom SMS sending logic
        // Could integrate with other services, databases, etc.
    }
}
```

Register custom drivers in your service provider:

```php
public function register(): void
{
    $this->app->bind('notifyre.custom_driver', CustomDriver::class);
}
```

### 2. Custom Services

Extend the main service for additional functionality:

```php
<?php

namespace App\Services;

use Arbi\Notifyre\Services\NotifyreService;

class ExtendedNotifyreService extends NotifyreService
{
    public function sendWithLogging(RequestBodyDTO $message): void
    {
        // Log before sending
        logger('Sending SMS', ['message' => $message]);
        
        // Send via parent
        parent::send($message);
        
        // Log after sending
        logger('SMS sent successfully');
    }
}
```

### 3. Custom Facades

Create custom facades for specific use cases:

```php
<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CustomNotifyre extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'notifyre.custom';
    }
}
```

## Configuration Management

### Configuration Merging

The package merges its default configuration with application config:

```php
$this->mergeConfigFrom(self::CONFIG_PATH, 'notifyre');
```

### Configuration Publishing

Configuration can be published to the application:

```php
$this->publishes([
    self::CONFIG_PATH => config_path('notifyre.php'),
], 'notifyre-config');
```

### Environment Variables

Environment variables are automatically loaded and merged with configuration.

## Error Handling

### Exception Hierarchy

```php
InvalidArgumentException (Base)
├── InvalidConfigurationException
├── ApiException
└── ConnectionException
```

### Error Handling Strategy

1. **Configuration Errors**: Thrown during service initialization
2. **Validation Errors**: Thrown when DTOs are invalid
3. **API Errors**: Thrown when external API calls fail
4. **Network Errors**: Thrown when HTTP requests fail

## Performance Considerations

### 1. Singleton Pattern

Services are registered as singletons to avoid repeated instantiation:

```php
$this->app->singleton('notifyre', NotifyreService::class);
```

### 2. Lazy Loading

Drivers are created only when needed:

```php
public function send(RequestBodyDTO $message): void
{
    $driver = $this->driverFactory->create(); // Created on demand
    $driver->send($message);
}
```

### 3. Caching

Optional response caching for API calls:

```php
'cache' => [
    'enabled' => env('NOTIFYRE_CACHE_ENABLED', true),
    'ttl' => env('NOTIFYRE_CACHE_TTL', 3600),
    'prefix' => env('NOTIFYRE_CACHE_PREFIX', 'notifyre_'),
],
```

## Testing Architecture

### 1. Mocking Strategy

The package is designed for easy testing:

```php
// Mock the service
$mockService = Mockery::mock(NotifyreServiceInterface::class);
$mockService->shouldReceive('send')->once();

// Bind mock to container
$this->app->instance('notifyre', $mockService);
```

### 2. Test Drivers

The log driver provides cost-free testing:

```php
NOTIFYRE_DRIVER=log  // No actual SMS sent
```

### 3. Test Helpers

Built-in test helpers for common scenarios:

```php
// Create test message
$message = new RequestBodyDTO(
    body: 'Test message',
    sender: 'TestApp',
    recipients: [new Recipient('mobile_number', '+1234567890')]
);
```

## Security Considerations

### 1. API Key Protection

API keys are stored in environment variables, never in code.

### 2. Input Validation

All inputs are validated through DTOs:

```php
private function validate(): void
{
    if (empty(trim($this->body))) {
        throw new InvalidArgumentException('Body cannot be empty');
    }
    
    if (empty($this->recipients)) {
        throw new InvalidArgumentException('Recipients array cannot be empty');
    }
}
```

### 3. Rate Limiting

Built-in rate limiting to prevent abuse:

```php
'rate_limiting' => [
    'delay_between_sms' => env('NOTIFYRE_SMS_DELAY', 1),
    'max_per_minute' => env('NOTIFYRE_MAX_PER_MINUTE', 60),
],
```

## Best Practices

### 1. Architecture

- **Single Responsibility**: Each class has one clear purpose
- **Dependency Injection**: Services receive dependencies through constructor
- **Interface Segregation**: Small, focused interfaces
- **Open/Closed**: Open for extension, closed for modification

### 2. Error Handling

- **Fail Fast**: Validate inputs early
- **Clear Messages**: Provide helpful error messages
- **Graceful Degradation**: Handle errors without crashing
- **Logging**: Log errors for debugging

### 3. Performance

- **Lazy Loading**: Create resources when needed
- **Caching**: Cache expensive operations
- **Connection Pooling**: Reuse HTTP connections
- **Async Processing**: Use queues for bulk operations

## Troubleshooting

### Common Issues

1. **Service Not Found**: Check service provider registration
2. **Configuration Missing**: Run `php artisan notifyre:publish-config`
3. **Driver Errors**: Verify driver configuration
4. **API Errors**: Check API key and network connectivity

### Debug Mode

Enable debug mode for detailed error information:

```php
APP_DEBUG=true
LOG_LEVEL=debug
```

## Next Steps

1. [Learn about commands](./COMMANDS.md)
2. [See usage examples](./USAGE.md)
3. [Explore configuration options](./CONFIGURATION.md)
4. [Understand drivers](./DRIVERS.md)
5. [Review examples](./EXAMPLES.md)
