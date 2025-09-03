# Architecture

How the Notifyre package is structured and designed.

## Overview

The package follows a **driver-based architecture** that separates SMS sending logic from the rest of your application,
with rich DTOs that implement Laravel's Arrayable interface, database persistence, and REST API endpoints.

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
                                │
                                ▼
                       ┌──────────────────┐
                       │ HTTP Controllers │
                       │ (REST API)       │
                       └──────────────────┘
                                │
                                ▼
                       ┌──────────────────┐
                       │ Database Models  │
                       │ (Persistence)    │
                       └──────────────────┘
```

## Key Classes

### NotifyreService

The main service for direct SMS sending:

```php
class NotifyreService
{
    public function send(RequestBody $message): ?ResponseBody
    {
        return $this->create()->send($message);
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
private function create(): LogDriver|SMSDriver
{
    $driver = config('services.notifyre.driver') ?? config('notifyre.driver');
    
    return match ($driver) {
        NotifyreDriver::LOG->value => new LogDriver(),
        NotifyreDriver::SMS->value => new SMSDriver(),
        default => throw new InvalidArgumentException("Invalid driver: {$driver}")
    };
}
```

**Features:**

- Configuration-based driver selection
- Validation of driver values
- Easy extension for custom drivers

### Drivers

Implement the driver pattern:

```php
// Both drivers implement similar interfaces
class SMSDriver
{
    public function send(RequestBody $message): ?ResponseBody
    {
        // Send via Notifyre API
    }
}

class LogDriver
{
    public function send(RequestBody $message): ?ResponseBody
    {
        // Log to Laravel logs
        return null;
    }
}
```

- **SMSDriver**: Sends real SMS via Notifyre API and returns response data
- **LogDriver**: Logs SMS to Laravel logs and returns null

### NotifyreChannel

Handles Laravel notifications:

```php
class NotifyreChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        $request = $notification->toNotifyre();
        $this->service->send($request);
    }
}
```

### HTTP Controllers

Provide REST API endpoints:

```php
class NotifyreSMSController extends Controller
{
    public function store(NotifyreSMSMessagesRequest $request): JsonResponse
    {
        // Send SMS and optionally persist to database
    }
    
    public function index(Request $request): JsonResponse
    {
        // List SMS messages with pagination
    }
    
    public function show(int $sms): JsonResponse
    {
        // Get specific SMS message
    }
}
```

### Database Models

Store SMS messages and recipients:

```php
class NotifyreSMSMessages extends Model
{
    protected $fillable = [
        'messageId',
        'sender',
        'body',
    ];
    
    public function messageRecipients(): HasMany
    {
        return $this->hasMany(NotifyreSMSMessageRecipient::class, 'sms_message_id');
    }
}
```

## DTO Architecture

### RequestBody

Rich data transfer object for SMS requests:

```php
readonly class RequestBody implements Arrayable
{
    public function __construct(
        public string $body,
        public array $recipients,
        public ?string $sender = null,
    ) {
        // Validation
        if (empty(trim($body))) {
            throw new InvalidArgumentException('Body cannot be empty');
        }
        if (empty($recipients)) {
            throw new InvalidArgumentException('Recipients cannot be empty');
        }
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
- **Type Safety**: Readonly properties with proper typing

### ResponseBody

Structured response data:

```php
readonly class ResponseBody implements Arrayable
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
    ) {
        if (!in_array($type, NotifyreRecipientTypes::values())) {
            throw new InvalidArgumentException("Invalid type '$type'");
        }
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Value cannot be empty');
        }
    }
    
    public function toArray(): array
    {
        return ['type' => $this->type, 'value' => $this->value];
    }
}
```

**Supported Types:**

- `mobile_number` - Direct phone number
- `contact` - Contact from Notifyre account
- `group` - Group from Notifyre account

## Data Flow

### Direct SMS

1. `notifyre()->send($message)` calls `NotifyreService::send()`
2. Service gets driver from `DriverFactory`
3. Driver processes the message (API call or logging)
4. Response data is returned to caller
5. Caller can handle success/failure and access message details

### Notifications

1. `$user->notify($notification)` triggers Laravel's notification system
2. `NotifyreChannel` receives the notification
3. Channel calls `$notification->toNotifyre()` to get message data
4. Message is sent through the appropriate driver
5. Response data is available for error handling

### REST API

1. HTTP request comes to `NotifyreSMSController`
2. Request is validated using `NotifyreSMSMessagesRequest`
3. Message is sent via `NotifyreService`
4. Optionally persisted to database if enabled
5. Response is cached if caching is enabled
6. JSON response returned to client

## Design Patterns

### Strategy Pattern

Drivers implement different strategies for SMS processing:

- **SMS Strategy**: Send via API and return real response
- **Log Strategy**: Log to files and return null

### Factory Pattern

`DriverFactory` creates the right driver based on configuration.

### Facade Pattern

`Notifyre` facade provides easy access to the service.

### Repository Pattern

Database operations are handled through service classes.

### Dependency Injection

All dependencies are injected through Laravel's service container.

### Data Transfer Object Pattern

Rich DTOs with validation and Arrayable interface for easy data manipulation.

## Extension Points

### Custom Drivers

Create your own driver by implementing the driver pattern:

```php
class CustomDriver
{
    public function send(RequestBody $message): ?ResponseBody
    {
        // Your custom SMS logic
        return new ResponseBody(/* ... */);
    }
}
```

### Custom Services

Extend `NotifyreService` for additional functionality:

```php
class CustomNotifyreService extends NotifyreService
{
    public function sendWithRetry(RequestBody $message, int $retries): ?ResponseBody
    {
        // Custom retry logic
    }
}
```

## Service Providers

The package registers itself through:

- **`NotifyreServiceProvider`**: Main service provider
- **`ConfigurationServiceProvider`**: Configuration management
- **`MigrationServiceProvider`**: Database migrations
- **`ServicesServiceProvider`**: Service bindings
- **`CommandServiceProvider`**: Artisan commands

## Contracts

Key interfaces that define the package's API:

- `NotifyreManager`: Main service contract
- Driver contracts for SMS and logging operations

## Benefits

- **Separation of Concerns**: SMS logic is isolated
- **Testability**: Easy to mock and test
- **Flexibility**: Switch drivers without code changes
- **Extensibility**: Add custom drivers easily
- **Laravel Integration**: Follows Laravel conventions
- **Rich DTOs**: Comprehensive data objects with validation
- **Response Handling**: Full response data for tracking and debugging
- **Arrayable Interface**: Easy data manipulation and serialization
- **Database Persistence**: Store SMS messages and recipients
- **REST API**: Full HTTP API with rate limiting and caching
- **Rate Limiting**: Built-in protection against abuse

## Performance Considerations

### Singleton Pattern

Services are registered as singletons to avoid repeated instantiation.

### Lazy Loading

Drivers are created only when needed.

### Caching

Optional response caching for API calls.

### Database Optimization

Efficient database queries with proper relationships.

## Testing Architecture

### Mocking Strategy

The package is designed for easy testing:

```php
// Mock the service
$mockService = Mockery::mock(NotifyreService::class);
$mockService->shouldReceive('send')->once()->andReturn($mockResponse);

// Bind mock to container
$this->app->instance('notifyre', $mockService);
```

### Test Drivers

The log driver provides cost-free testing:

```php
NOTIFYRE_DRIVER=log  // No actual SMS sent, logs to Laravel logs
```

## Security Considerations

### API Key Protection

API keys are stored in environment variables, never in code.

### Input Validation

All inputs are validated through DTOs with comprehensive validation rules.

### Rate Limiting

Built-in rate limiting to prevent abuse.

### Middleware Support

Customizable middleware stack for API endpoints.

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

### API Design

- **RESTful**: Follow REST conventions
- **Validation**: Comprehensive input validation
- **Rate Limiting**: Protect against abuse
- **Caching**: Improve performance where appropriate
- **Error Handling**: Consistent error responses

## Next Steps

- [Learn about drivers](./DRIVERS.md)
- [See usage examples](./../usage/DIRECT_SMS.md)
- [Configure the package](./../getting-started/CONFIGURATION.md)
- [Explore API usage](./../usage/API.md)
