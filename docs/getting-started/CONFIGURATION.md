# Configuration

Configure the Notifyre package to match your needs.

## Environment Variables

### Required

```env
# Choose your driver
NOTIFYRE_DRIVER=log

# API key for SMS driver
NOTIFYRE_API_KEY=your_api_key_here
```

### Optional

```env
# Default sender number
NOTIFYRE_SMS_FROM=+1234567890

# Default recipient (for testing)
NOTIFYRE_SMS_RECIPIENT=+1234567890

# API settings
NOTIFYRE_BASE_URL=https://api.notifyre.com
NOTIFYRE_TIMEOUT=30

# Retry settings
NOTIFYRE_RETRY_TIMES=3
NOTIFYRE_RETRY_SLEAP=1000

# Rate limiting
NOTIFYRE_RATE_LIMIT_MAX_REQUESTS=60
NOTIFYRE_RATE_LIMIT_DECAY_MINUTES=1

# Advanced DTO settings
NOTIFYRE_MAX_METADATA_KEYS=50
NOTIFYRE_MAX_METADATA_KEY_LENGTH=50
NOTIFYRE_MAX_METADATA_VALUE_LENGTH=500
```

## Configuration File

The package creates `config/notifyre.php` with these sections:

### Driver Settings

```php
'driver' => env('NOTIFYRE_DRIVER', 'log'),
'api_key' => env('NOTIFYRE_API_KEY', ''),
```

### API Settings

```php
'base_url' => env('NOTIFYRE_BASE_URL', 'https://api.notifyre.com'),
'timeout' => env('NOTIFYRE_TIMEOUT', 30),
```

### Retry Logic

```php
'retry' => [
    'times' => env('NOTIFYRE_RETRY_TIMES', 3),
    'sleep' => env('NOTIFYRE_RETRY_SLEEP', 1000),
],
```

### Rate Limiting

```php
'rate_limit' => [
    'enabled' => env('NOTIFYRE_RATE_LIMIT_ENABLED', true),
    'max_requests' => env('NOTIFYRE_RATE_LIMIT_MAX_REQUESTS', 60),
    'decay_minutes' => env('NOTIFYRE_RATE_LIMIT_DECAY_MINUTES', 1),
],
```

### DTO Validation Settings

```php
'dto' => [
    'max_metadata_keys' => env('NOTIFYRE_MAX_METADATA_KEYS', 50),
    'max_metadata_key_length' => env('NOTIFYRE_MAX_METADATA_KEY_LENGTH', 50),
    'max_metadata_value_length' => env('NOTIFYRE_MAX_METADATA_VALUE_LENGTH', 500),
],
```

## Driver-Specific Configuration

### SMS Driver

```env
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_api_key
NOTIFYRE_BASE_URL=https://api.notifyre.com
NOTIFYRE_TIMEOUT=30
NOTIFYRE_RETRY_TIMES=3
```

### Log Driver

```env
NOTIFYRE_DRIVER=log
# No additional configuration needed
# Returns mock ResponseBodyDTO for testing
```

## Publishing Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Arbi\Notifyre\Providers\NotifyreServiceProvider"
```

## Environment-Specific Configs

### Development

```env
NOTIFYRE_DRIVER=log
NOTIFYRE_SMS_FROM=+1234567890
```

### Production

```env
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_production_key
NOTIFYRE_SMS_FROM=+1234567890
NOTIFYRE_TIMEOUT=30
```

### Testing

```env
NOTIFYRE_DRIVER=log
NOTIFYRE_SMS_FROM=+1234567890
```

## Validation

The package validates your configuration:

- **Driver**: Must be `sms` or `log`
- **API Key**: Required when using `sms` driver
- **Base URL**: Must be a valid URL
- **Timeout**: Must be a positive integer
- **Retry Times**: Must be a positive integer
- **Metadata Limits**: Enforced at DTO level for data integrity

## Advanced Configuration

### Metadata Validation

The package enforces metadata limits to prevent API issues:

```env
# Maximum number of metadata key-value pairs
NOTIFYRE_MAX_METADATA_KEYS=50

# Maximum length for metadata keys
NOTIFYRE_MAX_METADATA_KEY_LENGTH=50

# Maximum length for metadata values
NOTIFYRE_MAX_METADATA_VALUE_LENGTH=500
```

### Response Handling

Both drivers now return `ResponseBodyDTO` objects:

- **SMS Driver**: Returns real API response data
- **Log Driver**: Returns mock response data for testing

## Next Steps

- [Learn about drivers](./../technical/DRIVERS.md)
- [See usage examples](./../usage/DIRECT_SMS.md)
- [Set up notifications](./../usage/NOTIFICATIONS.md)
