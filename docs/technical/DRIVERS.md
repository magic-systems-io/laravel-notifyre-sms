# Drivers

Drivers determine how SMS messages are processed - either sent to the Notifyre API or logged for testing. Each driver has different behavior and return values.

## Available Drivers

### SMS Driver (`sms`)

Sends real SMS messages through the Notifyre API and returns actual response data.

```env
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_api_key_here
```

**Use for:** Production environments where you want to send actual SMS messages and track delivery status.

**Returns:** `ResponseBody` with real API response data including message IDs, status codes, and error details.

### Log Driver (`log`)

Logs SMS messages to Laravel logs instead of sending them.

```env
NOTIFYRE_DRIVER=log
```

**Use for:** Development, testing, and staging environments.

**Returns:** `null` (messages are logged to Laravel logs for debugging).

## How Drivers Work

1. **Driver Selection**: Set `NOTIFYRE_DRIVER` in your `.env` file
2. **Driver Creation**: The `NotifyreService` creates the appropriate driver
3. **Message Processing**: Your SMS message is sent to the selected driver
4. **Response**: Driver returns response data or null based on implementation

## Driver Creation

The package automatically selects the right driver based on your configuration:

```php
// In NotifyreService
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
5. **Returns `ResponseBody`** with real API response data

### Log Driver

1. Validates your message
2. Logs to `storage/logs/laravel.log`
3. Includes sender, recipient, and message body
4. No external API calls
5. **Returns `null`** (no response data)

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

// Always returns null, check logs for message details
if ($response === null) {
    echo "Message logged successfully (check Laravel logs)";
} else {
    echo "Unexpected response from log driver";
}
```

## Testing with Drivers

### Unit Tests

```php
// In your test
Config::set('notifyre.driver', 'log');

// SMS will be logged, not sent, returns null
$response = notifyre()->send($message);
$this->assertNull($response);
```

### Manual Testing

```bash
# Test with log driver
NOTIFYRE_DRIVER=log php artisan sms:send --message="Test message"

# Check logs
tail -f storage/logs/laravel.log
```

## Driver Implementation

Drivers follow a consistent pattern but have different return values:

```php
class SMSDriver
{
    public function send(RequestBody $message): ?ResponseBody
    {
        // Send via Notifyre API
        // Return ResponseBody with real data
    }
}

class LogDriver
{
    public function send(RequestBody $message): ?ResponseBody
    {
        // Log to Laravel logs
        Log::info('SMS Message', [
            'body' => $message->body,
            'recipients' => $message->recipients,
            'sender' => $message->sender,
        ]);
        
        return null; // No response data
    }
}
```

## Custom Drivers

When creating custom drivers, you can choose the return type based on your needs:

```php
class CustomDriver
{
    public function send(RequestBody $message): ?ResponseBody
    {
        // Your custom SMS logic
        
        // Option 1: Return response data
        return new ResponseBody(
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
        
        // Option 2: Return null (like log driver)
        // return null;
    }
}
```

## Driver Priority

The package checks configuration in this order:

1. `config('services.notifyre.driver')` - Laravel services config
2. `config('notifyre.driver')` - Package config
3. Falls back to default value

This follows Laravel conventions and allows for flexible configuration.

## Error Handling

### SMS Driver Errors

- **Connection Errors**: Network issues, timeouts
- **API Errors**: Invalid API key, rate limiting
- **Validation Errors**: Invalid phone numbers, empty messages
- **Response Errors**: API returns error status

### Log Driver Errors

- **Validation Errors**: Invalid input data
- **Logging Errors**: File system issues (rare)

## Performance Considerations

### SMS Driver

- **Network Latency**: Depends on Notifyre API response time
- **Rate Limiting**: Respects API limits
- **Retry Logic**: Configurable retry attempts
- **Caching**: Optional response caching

### Log Driver

- **Fast**: No network calls
- **Lightweight**: Simple file logging
- **No Limits**: Can handle high message volumes
- **Debugging**: Full message details in logs

## Next Steps

- [Learn about the architecture](./ARCHITECTURE.md)
- [See usage examples](./../usage/DIRECT_SMS.md)
- [Configure the package](./../getting-started/CONFIGURATION.md)
- [Explore API usage](./../usage/API.md)
