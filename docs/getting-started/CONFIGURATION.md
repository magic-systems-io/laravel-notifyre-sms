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
NOTIFYRE_SMS_SENDER=YourAppName

# Country code prefix for numbers
NOTIFYRE_DEFAULT_NUMBER_PREFIX=+1

# API settings
NOTIFYRE_BASE_URL=https://api.notifyre.com
NOTIFYRE_TIMEOUT=30

# Retry settings
NOTIFYRE_RETRY_TIMES=3
NOTIFYRE_RETRY_SLEEP=1000
```

### API Configuration

```env
# Enable/disable API endpoints
NOTIFYRE_API_ENABLED=true

# API route prefix
NOTIFYRE_API_PREFIX=notifyre

# API middleware
NOTIFYRE_API_MIDDLEWARE=api

# Rate limiting
NOTIFYRE_RATE_LIMIT_ENABLED=true
NOTIFYRE_RATE_LIMIT_MAX_REQUESTS=60
NOTIFYRE_RATE_LIMIT_DECAY_MINUTES=1

# Database persistence
NOTIFYRE_DB_ENABLED=true

# Caching
NOTIFYRE_CACHE_ENABLED=true
NOTIFYRE_CACHE_TTL=3600
NOTIFYRE_CACHE_PREFIX=notifyre_
```

## Configuration File

The package creates `config/notifyre.php` with these sections:

### Driver Settings

```php
'driver' => env('NOTIFYRE_DRIVER', 'log'),
'api_key' => env('NOTIFYRE_API_KEY', ''),
```

### Default Settings

```php
'default_sender' => env('NOTIFYRE_SMS_SENDER', env('APP_NAME')),
'default_number_prefix' => env('NOTIFYRE_DEFAULT_NUMBER_PREFIX', ''),
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

### API Configuration

```php
'api' => [
    'enabled' => env('NOTIFYRE_API_ENABLED', true),
    'prefix' => env('NOTIFYRE_API_PREFIX', 'notifyre'),
    'middleware' => explode(',', env('NOTIFYRE_API_MIDDLEWARE', 'api')),
    'rate_limit' => [
        'enabled' => env('NOTIFYRE_RATE_LIMIT_ENABLED', true),
        'max_requests' => env('NOTIFYRE_RATE_LIMIT_MAX_REQUESTS', 60),
        'decay_minutes' => env('NOTIFYRE_RATE_LIMIT_DECAY_MINUTES', 1),
    ],
    'database' => [
        'enabled' => env('NOTIFYRE_DB_ENABLED', true),
    ],
    'cache' => [
        'enabled' => env('NOTIFYRE_CACHE_ENABLED', true),
        'ttl' => env('NOTIFYRE_CACHE_TTL', 3600),
        'prefix' => env('NOTIFYRE_CACHE_PREFIX', 'notifyre_'),
    ],
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
# Logs messages to Laravel logs for testing
```

## Publishing Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="MagicSystemsIO\Notifyre\Providers\NotifyreServiceProvider"
```

## Environment-Specific Configs

### Development

```env
NOTIFYRE_DRIVER=log
NOTIFYRE_SMS_SENDER=YourAppName
```

### Production

```env
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_production_key
NOTIFYRE_SMS_SENDER=YourAppName
NOTIFYRE_BASE_URL=https://api.notifyre.com
NOTIFYRE_TIMEOUT=30
```

### Testing

```env
NOTIFYRE_DRIVER=log
NOTIFYRE_SMS_SENDER=YourAppName
```

## Validation

The package validates your configuration:

- **Driver**: Must be `sms` or `log`
- **API Key**: Required when using `sms` driver
- **Base URL**: Must be a valid URL
- **Timeout**: Must be a positive integer
- **Retry Times**: Must be a positive integer
- **Recipient Types**: Must be valid enum values

## Advanced Configuration

### API Features

The package provides comprehensive API functionality:

```env
# Enable REST API endpoints
NOTIFYRE_API_ENABLED=true

# Customize API routes
NOTIFYRE_API_PREFIX=notifyre

# Configure middleware
NOTIFYRE_API_MIDDLEWARE=auth:sanctum,throttle:60,1

# Rate limiting
NOTIFYRE_RATE_LIMIT_ENABLED=true
NOTIFYRE_RATE_LIMIT_MAX_REQUESTS=100
NOTIFYRE_RATE_LIMIT_DECAY_MINUTES=5
```

### Database Persistence

Store SMS messages and recipients in your database:

```env
# Enable database storage
NOTIFYRE_DB_ENABLED=true
```

### Caching

Cache API responses for improved performance:

```env
# Enable response caching
NOTIFYRE_CACHE_ENABLED=true

# Cache TTL in seconds
NOTIFYRE_CACHE_TTL=3600

# Cache key prefix
NOTIFYRE_CACHE_PREFIX=notifyre_
```

## Response Handling

Both drivers return appropriate responses:

- **SMS Driver**: Returns real API response data with delivery status
- **Log Driver**: Returns null (logs to Laravel logs for testing)

## Next Steps

- [Learn about drivers](./../technical/DRIVERS.md)
- [See usage examples](./../usage/DIRECT_SMS.md)
- [Set up notifications](./../usage/NOTIFICATIONS.md)
- [Explore API usage](./../usage/API.md)
