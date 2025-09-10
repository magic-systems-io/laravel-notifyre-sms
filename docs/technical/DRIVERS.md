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

## How Drivers Work

1. **Driver Selection**: Set `NOTIFYRE_DRIVER` in your `.env` file
2. **Driver Creation**: The `NotifyreService` creates the appropriate driver
3. **Message Processing**: Your SMS message is sent to the selected driver
4. **Response**: Driver returns response data or null based on implementation

## Driver Creation

The package automatically selects the right driver based on your configuration:

```php
// In NotifyreService
private static function createDriver(string $driver): SmsDriver
{
    return match ($driver) {
        NotifyreDriver::SMS->value => new SmsDriver(),
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
```

## Switching Drivers

### Development → Production

```env
# Development (.env.local)
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_test_key

# Production (.env)
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_production_key
```

### Testing

```env
# In phpunit.xml or .env.testing
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_test_key
```

## What Happens in Each Driver

### SMS Driver

1. Validates your message
2. Sends HTTP request to Notifyre API
3. Handles response and errors
4. Retries on failure (if configured)
5. **Returns `ResponseBody`** with real API response data

## Response Handling

### SMS Driver Response

```php
notifyre()->send($message);

// Message is sent and persisted to database if enabled
// Check database for message details
$message = NotifyreSmsMessages::latest()->first();
```

## Testing with Drivers

### Unit Tests

```php
// In your test
Config::set('notifyre.driver', 'sms');

// SMS will be sent via API
notifyre()->send($message);
```

## Driver Implementation

Drivers follow a consistent pattern:

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

## Custom Drivers

When creating custom drivers, you should return a ResponseBody:

```php
class CustomDriver
{
    public function send(RequestBody $request): ResponseBody
    {
        // Your custom SMS logic
        
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

## Performance Considerations

### SMS Driver

- **Network Latency**: Depends on Notifyre API response time
- **Rate Limiting**: Respects API limits
- **Retry Logic**: Configurable retry attempts
- **Database Persistence**: Messages are stored in database

## Next Steps

- [Learn about the architecture](./ARCHITECTURE.md)
- [See usage examples](./../usage/DIRECT_SMS.md)
- [Configure the package](./../getting-started/CONFIGURATION.md)
- [Explore API usage](./../usage/API.md)
