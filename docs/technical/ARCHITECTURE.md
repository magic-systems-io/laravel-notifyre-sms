# Architecture

How the Notifyre package is structured and designed.

## Overview

The package follows a **driver-based architecture** that separates SMS sending logic from the rest of your application, with database persistence and REST API endpoints.

## Core Components

```
NotifyreService (Direct SMS)
    ↓
SmsDriver (Production)
    ↓
NotifyreChannel (Notifications)
    ↓
NotifyreSmsController (REST API)
    ↓
Database Models (Persistence)
```

## Key Classes

### NotifyreService

The main service for direct SMS sending:

```php
class NotifyreService
{
    public static function send(RequestBody $request): void
    {
        $response = self::createDriver(self::getDriverName())->send($request);
        NotifyreMessagePersister::persist($request, $response);
    }
}
```

**Responsibilities:**

- Delegates SMS sending to appropriate driver
- Persists messages to database if enabled
- Handles errors and logging
- Maintains single responsibility principle

### Driver Creation

Creates the appropriate driver based on configuration:

```php
private static function createDriver(string $driver): SmsDriver
{
    return match ($driver) {
        NotifyreDriver::SMS->value => new SmsDriver(),
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
class SmsDriver
{
    public function send(RequestBody $request): ResponseBody
    {
        $response = ApiClientUtils::request(ApiUrlBuilder::buildSmsUrl(), $request, 'POST');
        return ResponseParser::parseSmsResponse($response->json(), $response->status());
    }
}
```

- **SmsDriver**: Sends real SMS via Notifyre API and returns response data
- **Log Driver**: Currently not implemented in the codebase

### NotifyreChannel

Handles Laravel notifications:

```php
class NotifyreChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        $requestBody = $notification->toNotifyre();
        if (!$requestBody instanceof RequestBody) {
            throw new InvalidArgumentException('Method `toNotifyre` must return RequestBodyDTO object.');
        }
        NotifyreService::send($requestBody);
    }
}
```

### HTTP Controllers

Provide REST API endpoints:

```php
class NotifyreSmsController extends Controller
{
    public function store(NotifyreSmsMessagesRequest $request): JsonResponse
    {
        NotifyreService::send($this->buildMessageData($request));
        return response()->json('Message is being sent', 201);
    }
    
    public function index(Request $request): JsonResponse
    {
        $sender = $request->user()?->getSender();
        $messages = NotifyreSmsMessages::where('sender', $sender)->get()->toArray();
        return response()->json($this->paginate($request, $messages));
    }
    
    public function show(string $sms): JsonResponse
    {
        $message = NotifyreSmsMessages::with('messageRecipients.recipient')->find($sms);
        return response()->json($message);
    }
}
```

### Database Models

Store SMS messages and recipients:

```php
class NotifyreSmsMessages extends Model
{
    protected $fillable = [
        'id',
        'sender',
        'body',
        'driver',
    ];
    
    public function messageRecipients(): HasMany
    {
        return $this->hasMany(NotifyreSmsMessageRecipient::class, 'sms_message_id');
    }
    
    public function recipients(): HasManyThrough
    {
        return $this->hasManyThrough(
            NotifyreRecipients::class,
            NotifyreSmsMessageRecipient::class,
            'sms_message_id',
            'id',
            'id',
            'recipient_id'
        );
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
        public ?int $scheduledDate = null,
        public ?bool $addUnsubscribeLink = null,
        public ?string $callbackUrl = null,
        public ?array $metadata = null,
        public ?string $campaignName = null,
    ) {
        if (empty(trim($body))) {
            throw new InvalidArgumentException('Body cannot be empty');
        }
        if (empty($recipients)) {
            throw new InvalidArgumentException('Recipients cannot be empty');
        }
    }
    
    public function toArray(): array
    {
        $recipients = array_map(fn (Recipient $recipient) => $recipient->toArray(), $this->recipients);
        return array_filter([
            'Body' => $this->body,
            'Recipients' => $recipients,
            'From' => $this->sender,
            'ScheduledDate' => $this->scheduledDate,
            'AddUnsubscribeLink' => $this->addUnsubscribeLink,
            'CallbackUrl' => $this->callbackUrl,
            'Metadata' => $this->metadata,
            'CampaignName' => $this->campaignName,
        ], fn ($value) => $value !== null);
    }
}
```

**Features:**

- **Arrayable Interface**: Easy conversion to arrays and JSON
- **Comprehensive Validation**: Built-in input validation
- **Type Safety**: Readonly properties with proper typing

### Recipient

Enhanced recipient object:

```php
class Recipient implements Arrayable
{
    public function __construct(
        public string $type,
        public string $value,
    ) {
        $this->normalizeCountryCode();
        if (!RecipientVerificationUtils::validateRecipient($this->value, $this->type)) {
            throw new InvalidArgumentException("Invalid recipient '$value' for type '$type'.");
        }
    }
    
    private function normalizeCountryCode(): void
    {
        if (!str_starts_with($this->value, '+')) {
            $defaultPrefix = config('notifyre.default_number_prefix');
            if (empty($defaultPrefix)) {
                throw new InvalidArgumentException('Recipient number must include country code or set a default prefix in configuration.');
            }
            $this->value = preg_replace('/^0/', $defaultPrefix, $this->value);
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
2. Service creates appropriate driver based on configuration
3. Driver processes the message (API call)
4. Message is persisted to database if enabled
5. Response data is returned to caller

### Notifications

1. `$user->notify($notification)` triggers Laravel's notification system
2. `NotifyreChannel` receives the notification
3. Channel calls `$notification->toNotifyre()` to get message data
4. Message is sent through `NotifyreService`
5. Message is persisted to database if enabled

### REST API

1. HTTP request comes to `NotifyreSmsController`
2. Request is validated using `NotifyreSmsMessagesRequest`
3. Message is sent via `NotifyreService`
4. Message is persisted to database if enabled
5. JSON response returned to client

## Design Patterns

### Strategy Pattern

Drivers implement different strategies for SMS processing:

- **SMS Strategy**: Send via API and return real response

### Factory Pattern

Driver creation based on configuration.

### Facade Pattern

`notifyre()` helper function provides easy access to the service.

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
    public function send(RequestBody $request): ResponseBody
    {
        // Your custom SMS logic
        return new ResponseBody(/* ... */);
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
- **`RouteServiceProvider`**: API routes
- **`NotifyreLoggingServiceProvider`**: Logging configuration

## Contracts

Key interfaces that define the package's API:

- `NotifyreManager`: Main service contract

## Benefits

- **Separation of Concerns**: SMS logic is isolated
- **Testability**: Easy to mock and test
- **Flexibility**: Switch drivers without code changes
- **Extensibility**: Add custom drivers easily
- **Laravel Integration**: Follows Laravel conventions
- **Rich DTOs**: Comprehensive data objects with validation
- **Database Persistence**: Store SMS messages and recipients
- **REST API**: Full HTTP API with rate limiting
- **Rate Limiting**: Built-in protection against abuse

## Performance Considerations

### Singleton Pattern

Services are registered as singletons to avoid repeated instantiation.

### Lazy Loading

Drivers are created only when needed.

### Database Optimization

Efficient database queries with proper relationships.

## Testing Architecture

### Mocking Strategy

The package is designed for easy testing:

```php
// Mock the service
$mockService = Mockery::mock(NotifyreService::class);
$mockService->shouldReceive('send')->once();

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

### API Design

- **RESTful**: Follow REST conventions
- **Validation**: Comprehensive input validation
- **Rate Limiting**: Protect against abuse
- **Error Handling**: Consistent error responses

## Next Steps

- [Learn about drivers](./DRIVERS.md)
- [See usage examples](./../usage/DIRECT_SMS.md)
- [Configure the package](./../getting-started/CONFIGURATION.md)
- [Explore API usage](./../usage/API.md)
