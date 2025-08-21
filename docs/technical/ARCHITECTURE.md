# Architecture

How the Notifyre package is structured and designed.

## Overview

The package follows a **driver-based architecture** that separates SMS sending logic from the rest of your application.

## Core Components

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│ NotifyreService │───▶│  DriverFactory   │───▶│  SMSDriver      │
│  (Direct SMS)   │    │                  │    │  LogDriver      │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                                │
                                ▼
                       ┌──────────────────┐
                       │ NotifyreChannel  │
                       │ (Notifications)  │
                       └──────────────────┘
```

## Key Classes

### NotifyreService

The main service for direct SMS sending:

```php
class NotifyreService implements NotifyreServiceInterface
{
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

### DriverFactory

Creates the appropriate driver based on configuration:

```php
class DriverFactory implements NotifyreDriverFactoryInterface
{
    public function create(): NotifyreDriverInterface
    {
        $driver = config('notifyre.driver');
        
        return match($driver) {
            'sms' => new SMSDriver(),
            'log' => new LogDriver(),
            default => throw new InvalidArgumentException("Unknown driver: {$driver}")
        };
    }
}
```

**Features:**
- Configuration-based driver selection
- Validation of driver values
- Easy extension for custom drivers

### Drivers

Implement the `NotifyreDriverInterface`:

```php
interface NotifyreDriverInterface
{
    public function send(RequestBodyDTO $message): void;
}
```

- **SMSDriver**: Sends real SMS via Notifyre API
- **LogDriver**: Logs SMS to Laravel logs

### NotifyreChannel

Handles Laravel notifications:

```php
class NotifyreChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        $this->driverFactory->create()->send($notification->toNotifyre());
    }
}
```

## Data Flow

### Direct SMS

1. `notifyre()->send($message)` calls `NotifyreService::send()`
2. Service gets driver from `DriverFactory`
3. Driver processes the message (API call or logging)
4. Result is returned to caller

### Notifications

1. `$user->notify($notification)` triggers Laravel's notification system
2. `NotifyreChannel` receives the notification
3. Channel calls `$notification->toNotifyre()` to get message data
4. Message is sent through the appropriate driver

## Design Patterns

### Strategy Pattern

Drivers implement different strategies for SMS processing:
- **SMS Strategy**: Send via API
- **Log Strategy**: Log to files

### Factory Pattern

`DriverFactory` creates the right driver based on configuration.

### Facade Pattern

`Notifyre` facade provides easy access to the service.

### Dependency Injection

All dependencies are injected through Laravel's service container.

## Extension Points

### Custom Drivers

Create your own driver by implementing `NotifyreDriverInterface`:

```php
class CustomDriver implements NotifyreDriverInterface
{
    public function send(RequestBodyDTO $message): void
    {
        // Your custom SMS logic
    }
}
```

### Custom Services

Extend `NotifyreService` for additional functionality:

```php
class CustomNotifyreService extends NotifyreService
{
    public function sendWithRetry(RequestBodyDTO $message, int $retries): void
    {
        // Custom retry logic
    }
}
```

## Service Providers

The package registers itself through:

- **`NotifyreServiceProvider`**: Main service provider
- **`PackageServiceProvider`**: Package-level configuration
- **`ContractServiceProvider`**: Interface bindings
- **`FacadeServiceProvider`**: Facade registration

## Contracts

Key interfaces that define the package's API:

- `NotifyreServiceInterface`: Main service contract
- `NotifyreDriverInterface`: Driver contract
- `NotifyreDriverFactoryInterface`: Factory contract

## Benefits

- **Separation of Concerns**: SMS logic is isolated
- **Testability**: Easy to mock and test
- **Flexibility**: Switch drivers without code changes
- **Extensibility**: Add custom drivers easily
- **Laravel Integration**: Follows Laravel conventions

## Performance Considerations

### Singleton Pattern

Services are registered as singletons to avoid repeated instantiation.

### Lazy Loading

Drivers are created only when needed.

### Caching

Optional response caching for API calls.

## Testing Architecture

### Mocking Strategy

The package is designed for easy testing:

```php
// Mock the service
$mockService = Mockery::mock(NotifyreServiceInterface::class);
$mockService->shouldReceive('send')->once();

// Bind mock to container
$this->app->instance('notifyre', $mockService);
```

### Test Drivers

The log driver provides cost-free testing:

```php
NOTIFYRE_DRIVER=log  // No actual SMS sent
```

## Security Considerations

### API Key Protection

API keys are stored in environment variables, never in code.

### Input Validation

All inputs are validated through DTOs.

### Rate Limiting

Built-in rate limiting to prevent abuse.

## Best Practices

### Architecture

- **Single Responsibility**: Each class has one clear purpose
- **Dependency Injection**: Services receive dependencies through constructor
- **Interface Segregation**: Small, focused interfaces
- **Open/Closed**: Open for extension, closed for modification

### Error Handling

- **Fail Fast**: Validate inputs early
- **Clear Messages**: Provide helpful error messages
- **Graceful Degradation**: Handle errors without crashing
- **Logging**: Log errors for debugging

## Next Steps

- [Learn about drivers](./DRIVERS.md)
- [See usage examples](./../usage/DIRECT_SMS.md)
- [Configure the package](./../getting-started/CONFIGURATION.md)
