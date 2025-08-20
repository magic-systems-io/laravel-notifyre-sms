# Notifyre Laravel Package - Configuration

## Quick Start (Minimal Configuration)

**You only need 2 environment variables to get started:**

```env
NOTIFYRE_DRIVER=log
NOTIFYRE_API_TOKEN=your_api_token_here
```

The package comes with sensible defaults for everything else. You can customize additional options later as needed.

## Configuration File

The package configuration is located at `config/notifyre.php` after publishing.

## Configuration Options

### Driver Configuration

```php
'driver' => env('NOTIFYRE_DRIVER', NotifyreDriver::LOG),
```

Choose between:

- **`sms`**: Send real SMS via Notifyre API
- **`log`**: Log SMS messages to Laravel logs (for testing)

### API Configuration

```php
'api_key' => env('NOTIFYRE_API_KEY', ''),
'base_url' => env('NOTIFYRE_BASE_URL', 'https://api.notifyre.com'),
'timeout' => env('NOTIFYRE_TIMEOUT', 30),
```

- **`api_key`**: Your Notifyre API token
- **`base_url`**: API endpoint (usually production)
- **`timeout`**: HTTP request timeout in seconds

### Retry Configuration

```php
'retry' => [
    'times' => env('NOTIFYRE_RETRY_TIMES', 3),
    'sleep' => env('NOTIFYRE_RETRY_SLEEP', 1000),
],
```

- **`times`**: Number of retry attempts for failed requests
- **`sleep`**: Milliseconds between retry attempts

### Rate Limiting

```php
'rate_limiting' => [
    'delay_between_sms' => env('NOTIFYRE_SMS_DELAY', 1),
    'max_per_minute' => env('NOTIFYRE_MAX_PER_MINUTE', 60),
],
```

- **`delay_between_sms`**: Seconds between SMS sends
- **`max_per_minute`**: Maximum SMS per minute

### Default Values

```php
'default_sender' => env('NOTIFYRE_SMS_SENDER', ''),
'default_recipient' => env('NOTIFYRE_SMS_RECIPIENT', ''),
'default_number_prefix' => env('NOTIFYRE_DEFAULT_NUMBER_PREFIX', ''),
```

- **`default_sender`**: Default sender ID for SMS
- **`default_recipient`**: Default recipient for testing
- **`default_number_prefix`**: Country code prefix (e.g., '+1' for US)

### Cache Configuration

```php
'cache' => [
    'enabled' => env('NOTIFYRE_CACHE_ENABLED', true),
    'ttl' => env('NOTIFYRE_CACHE_TTL', 3600),
    'prefix' => env('NOTIFYRE_CACHE_PREFIX', 'notifyre_'),
],
```

- **`enabled`**: Enable SMS response caching
- **`ttl`**: Cache time-to-live in seconds
- **`prefix`**: Cache key prefix

## Environment Variables

All configuration options can be set via environment variables. See [INSTALLATION.md](./INSTALLATION.md) for the
complete list.

## Testing Configuration

For development/testing, use the `log` driver:

```env
NOTIFYRE_DRIVER=log
```

This will log SMS messages to your Laravel logs instead of sending them.

## Production Configuration

For production, ensure you have:

```env
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_actual_api_key
NOTIFYRE_BASE_URL=https://api.notifyre.com
```

## Driver Details

### SMS Driver

- **Purpose**: Sends real SMS messages via Notifyre API
- **Use Case**: Production environments
- **Features**:
    - HTTP client with retry logic
    - Response caching support
    - Error handling and logging
    - Rate limiting support
    - Support for mobile numbers
- **Recipient Types**:
    - `mobile_number` - Direct phone numbers

### Log Driver

- **Purpose**: Logs SMS messages to Laravel logs for testing
- **Use Case**: Development, testing, CI/CD
- **Features**:
    - No external API calls
    - Fast execution
    - Easy debugging
    - No costs incurred
    - Support for mobile numbers

## Next Steps

1. [Learn about drivers](./DRIVERS.md)
2. [Learn how to use the package](./USAGE.md)
3. [See practical examples](./EXAMPLES.md)
