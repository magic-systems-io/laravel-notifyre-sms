# Architecture

How the Notifyre package is structured and designed.

## Overview

The package follows a **driver-based architecture** that separates SMS sending logic from the rest of your application, with rich DTOs that implement Laravel's Arrayable interface.

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
    public function send(RequestBodyDTO $message): ?ResponseBodyDTO
    {
        return $this->driverFactory->create()->send($message);
    }
}
```

**Responsibilities:**
- Delegates SMS sending to appropriate driver
- Returns response data for tracking and error handling
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
    public function send(RequestBodyDTO $message): ?ResponseBodyDTO;
}
```

- **SMSDriver**: Sends real SMS via Notifyre API and returns response data
- **LogDriver**: Logs SMS to Laravel logs and returns mock response data

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

## DTO Architecture

### RequestBodyDTO

Rich data transfer object for SMS requests:

```php
readonly class RequestBodyDTO implements Arrayable
{
    public function __construct(
        public string $body,
        public array $recipients,
        public ?string $from = null,
        public ?int $scheduledDate = null,
        public bool $addUnsubscribeLink = false,
        public ?string $callbackUrl = null,
        public array $metadata = [],
        public ?string $campaignName = null,
    ) {
        // Comprehensive validation
    }
    
    public function toArray(): array
    {
        // Convert to API format
    }
}
```

**Features:**
- **Arrayable Interface**: Easy conversion to arrays and JSON
- **Comprehensive Validation**: Built-in input validation
- **Flexible Parameters**: Support for scheduling, callbacks, metadata
- **Type Safety**: Readonly properties with proper typing

### ResponseBodyDTO

Structured response data:

```php
readonly class ResponseBodyDTO implements Arrayable
{
    public function __construct(
        public bool $success,
        public int $statusCode,
        public string $message,
        public ResponsePayload $payload,
        public array $errors,
    ) {}
    
    public function toArray(): array
    {
        // Convert to array format
    }
}
```

### Recipient

Enhanced recipient object:

```php
readonly class Recipient implements Arrayable
{
    public function __construct(
        public string $type,
        public string $value,
    ) {}
    
    public function toArray(): array
    {
        return ['type' => $this->type, 'value' => $this->value];
    }
}
```

**Supported Types:**
- `virtual_mobile_number` - Direct phone number
- `contact` - Contact from Notifyre account
- `group` - Group from Notifyre account

## Data Flow

### Direct SMS

1. `notifyre()->send($message)` calls `NotifyreService::send()`
2. Service gets driver from `DriverFactory`
3. Driver processes the message (API call or logging)
4. **Response data is returned to caller** (new behavior)
5. Caller can handle success/failure and access message details

### Notifications

1. `$user->notify($notification)` triggers Laravel's notification system
2. `NotifyreChannel` receives the notification
3. Channel calls `$notification->toNotifyre()` to get message data
4. Message is sent through the appropriate driver
5. Response data is available for error handling

## Design Patterns

### Strategy Pattern

Drivers implement different strategies for SMS processing:
- **SMS Strategy**: Send via API and return real response
- **Log Strategy**: Log to files and return mock response

### Factory Pattern

`DriverFactory` creates the right driver based on configuration.

### Facade Pattern

`Notifyre` facade provides easy access to the service.

### Dependency Injection

All dependencies are injected through Laravel's service container.

### Data Transfer Object Pattern

Rich DTOs with validation and Arrayable interface for easy data manipulation.

## Extension Points

### Custom Drivers

Create your own driver by implementing `NotifyreDriverInterface`:

```php
class CustomDriver implements NotifyreDriverInterface
{
    public function send(RequestBodyDTO $message): ?ResponseBodyDTO
    {
        // Your custom SMS logic
        return new ResponseBodyDTO(/* ... */);
    }
}
```

### Custom Services

Extend `NotifyreService` for additional functionality:

```php
class CustomNotifyreService extends NotifyreService
{
    public function sendWithRetry(RequestBodyDTO $message, int $retries): ?ResponseBodyDTO
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
- `NotifyreDriverInterface`: Driver contract with response return
- `NotifyreDriverFactoryInterface`: Factory contract

## Benefits

- **Separation of Concerns**: SMS logic is isolated
- **Testability**: Easy to mock and test
- **Flexibility**: Switch drivers without code changes
- **Extensibility**: Add custom drivers easily
- **Laravel Integration**: Follows Laravel conventions
- **Rich DTOs**: Comprehensive data objects with validation
- **Response Handling**: Full response data for tracking and debugging
- **Arrayable Interface**: Easy data manipulation and serialization

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
$mockService->shouldReceive('send')->once()->andReturn($mockResponse);

// Bind mock to container
$this->app->instance('notifyre', $mockService);
```

### Test Drivers

The log driver provides cost-free testing with mock responses:

```php
NOTIFYRE_DRIVER=log  // No actual SMS sent, returns mock ResponseBodyDTO
```

## Security Considerations

### API Key Protection

API keys are stored in environment variables, never in code.

### Input Validation

All inputs are validated through DTOs with comprehensive validation rules.

### Rate Limiting

Built-in rate limiting to prevent abuse.

## Best Practices

### Architecture

- **Single Responsibility**: Each class has one clear purpose
- **Dependency Injection**: Services receive dependencies through constructor
- **Interface Segregation**: Small, focused interfaces
- **Open/Closed**: Open for extension, closed for modification

### DTO Usage

- **Validation**: Use DTO validation for input sanitization
- **Arrayable**: Leverage `toArray()` method for data manipulation
- **Type Safety**: Use readonly properties and proper typing
- **Composition**: Build complex messages with multiple DTOs

### Error Handling

- **Fail Fast**: Validate inputs early
- **Clear Messages**: Provide helpful error messages
- **Graceful Degradation**: Handle errors without crashing
- **Logging**: Log errors for debugging
- **Response Data**: Use response DTOs for detailed error information

## Next Steps

- [Learn about drivers](./DRIVERS.md)
- [See usage examples](./../usage/DIRECT_SMS.md)
- [Configure the package](./../getting-started/CONFIGURATION.md)
