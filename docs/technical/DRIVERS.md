# Drivers

Drivers determine how SMS messages are processed - either sent to the Notifyre API or logged for testing. All drivers now return response data for better tracking and error handling.

## Available Drivers

### SMS Driver (`sms`)

Sends real SMS messages through the Notifyre API and returns actual response data.

```env
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_api_key_here
```

**Use for:** Production environments where you want to send actual SMS messages and track delivery status.

**Returns:** `ResponseBodyDTO` with real API response data including message IDs, status codes, and error details.

### Log Driver (`log`)

Logs SMS messages to Laravel logs instead of sending them and returns mock response data.

```env
NOTIFYRE_DRIVER=log
```

**Use for:** Development, testing, and staging environments.

**Returns:** `ResponseBodyDTO` with mock response data for testing response handling logic.

## How Drivers Work

1. **Driver Selection**: Set `NOTIFYRE_DRIVER` in your `.env` file
2. **Driver Creation**: The `DriverFactory` creates the appropriate driver
3. **Message Processing**: Your SMS message is sent to the selected driver
4. **Response**: Driver returns `ResponseBodyDTO` with success/failure details

## Driver Factory

The package automatically selects the right driver based on your configuration:

```php
// In NotifyreService
$driver = $this->driverFactory->create();
$response = $driver->send($message); // Now returns ResponseBodyDTO
```

## Configuration

### Environment Variables

```env
# Required for SMS driver
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_api_key

# Optional settings
NOTIFYRE_BASE_URL=https://api.notifyre.com
NOTIFYRE_TIMEOUT=30
NOTIFYRE_RETRY_TIMES=3
```

### Driver-Specific Settings

#### SMS Driver

```env
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_api_key
NOTIFYRE_BASE_URL=https://api.notifyre.com
NOTIFYRE_TIMEOUT=30
NOTIFYRE_RETRY_TIMES=3
NOTIFYRE_RETRY_SLEEP=1000
```

#### Log Driver

```env
NOTIFYRE_DRIVER=log
# No additional configuration needed
```

## Switching Drivers

### Development → Production

```env
# Development (.env.local)
NOTIFYRE_DRIVER=log

# Production (.env)
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_production_key
```

### Testing

```env
# In phpunit.xml or .env.testing
NOTIFYRE_DRIVER=log
```

## What Happens in Each Driver

### SMS Driver

1. Validates your message
2. Sends HTTP request to Notifyre API
3. Handles response and errors
4. Retries on failure (if configured)
5. **Returns `ResponseBodyDTO`** with real API response data

### Log Driver

1. Validates your message
2. Logs to `storage/logs/laravel.log`
3. Includes sender, recipient, and message body
4. No external API calls
5. **Returns `ResponseBodyDTO`** with mock response data for testing

## Response Handling

### SMS Driver Response

```php
$response = notifyre()->send($message);

if ($response && $response->success) {
    echo "Message sent! ID: " . $response->payload->smsMessageID;
} else {
    echo "Failed: " . $response->message;
    foreach ($response->errors as $error) {
        echo "Error: " . $error;
    }
}
```

### Log Driver Response

```php
$response = notifyre()->send($message);

// Always returns mock success response for testing
if ($response && $response->success) {
    echo "Message logged successfully (mock response)";
    echo "Mock ID: " . $response->payload->smsMessageID;
}
```

## Testing with Drivers

### Unit Tests

```php
// In your test
Config::set('notifyre.driver', 'log');

// SMS will be logged, not sent, but returns mock response
$response = notifyre()->send($message);
$this->assertTrue($response->success);
```

### Manual Testing

```bash
# Test with log driver
NOTIFYRE_DRIVER=log php artisan sms:send --message="Test message"

# Check logs
tail -f storage/logs/laravel.log
```

## Driver Implementation

Drivers implement the `NotifyreDriverInterface`:

```php
interface NotifyreDriverInterface
{
    public function send(RequestBodyDTO $message): ?ResponseBodyDTO;
}
```

This ensures all drivers work the same way regardless of implementation and return consistent response data.

## Custom Drivers

When creating custom drivers, ensure they return `ResponseBodyDTO`:

```php
class CustomDriver implements NotifyreDriverInterface
{
    public function send(RequestBodyDTO $message): ?ResponseBodyDTO
    {
        // Your custom SMS logic
        
        // Return response data
        return new ResponseBodyDTO(
            success: true,
            statusCode: 200,
            message: 'Message sent via custom driver',
            payload: new ResponsePayload(
                smsMessageID: 'custom_' . uniqid(),
                friendlyID: 'Custom-' . uniqid(),
                invalidToNumbers: []
            ),
            errors: []
        );
    }
}
```
