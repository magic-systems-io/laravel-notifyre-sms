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
# Country code prefix for numbers without country code
NOTIFYRE_DEFAULT_NUMBER_PREFIX=+1

# API settings
NOTIFYRE_BASE_URL=https://api.notifyre.com
NOTIFYRE_TIMEOUT=30
NOTIFYRE_RETRY_TIMES=3
NOTIFYRE_RETRY_SLEEP=1

# Routes configuration
NOTIFYRE_ROUTES_ENABLED=true
NOTIFYRE_ROUTE_PREFIX=notifyre
NOTIFYRE_RATE_LIMIT_ENABLED=true
NOTIFYRE_RATE_LIMIT_MAX=60
NOTIFYRE_RATE_LIMIT_WINDOW=1

# Feature toggles
NOTIFYRE_DB_ENABLED=true
NOTIFYRE_LOGGING_ENABLED=true
NOTIFYRE_LOG_PREFIX=notifyre_sms

# Webhook configuration
NOTIFYRE_WEBHOOK_RETRY_ATTEMPTS=3
NOTIFYRE_WEBHOOK_RETRY_DELAY=1
```

## Configuration File

The package creates `config/notifyre.php` with these sections:

### Driver Settings

```php
'driver' => env('NOTIFYRE_DRIVER', 'sms'),
'api_key' => env('NOTIFYRE_API_KEY'),
```

### Default Settings

```php
'default_number_prefix' => env('NOTIFYRE_DEFAULT_NUMBER_PREFIX', ''),
```

### HTTP Configuration

```php
'http' => [
    'base_url' => env('NOTIFYRE_BASE_URL', 'https://api.notifyre.com'),
    'timeout' => env('NOTIFYRE_TIMEOUT', 30), // seconds
    'retry' => [
        'times' => env('NOTIFYRE_RETRY_TIMES', 3),
        'sleep' => env('NOTIFYRE_RETRY_SLEEP', 1), // seconds between retries
    ],
],
```

### Routes Configuration

```php
'routes' => [
    'enabled' => env('NOTIFYRE_ROUTES_ENABLED', true),
    'prefix' => env('NOTIFYRE_ROUTE_PREFIX', 'notifyre'),
    'middleware' => ['api'],
    'rate_limit' => [
        'enabled' => env('NOTIFYRE_RATE_LIMIT_ENABLED', true),
        'max_requests' => env('NOTIFYRE_RATE_LIMIT_MAX', 60), // per minute
        'decay_minutes' => env('NOTIFYRE_RATE_LIMIT_WINDOW', 1),
    ],
],
```

### Database Configuration

```php
'database' => [
    'enabled' => env('NOTIFYRE_DB_ENABLED', true),
],
```

### Logging Configuration

```php
'logging' => [
    'enabled' => env('NOTIFYRE_LOGGING_ENABLED', true),
    'prefix' => env('NOTIFYRE_LOG_PREFIX', 'notifyre_sms'),
],
```

### Webhook Configuration

```php
'webhook' => [
    'retry_attempts' => env('NOTIFYRE_WEBHOOK_RETRY_ATTEMPTS', 3),
    'retry_delay' => env('NOTIFYRE_WEBHOOK_RETRY_DELAY', 1), // seconds between retries
],
```

## Driver-Specific Configuration

### SMS Driver

```env
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_api_key
NOTIFYRE_BASE_URL=https://api.notifyre.com
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
php artisan notifyre:publish-config
```

Or publish all configuration files:

```bash
php artisan notifyre:publish
```

## Environment-Specific Configs

### Development

```env
NOTIFYRE_DRIVER=log
NOTIFYRE_LOGGING_ENABLED=true
```

### Production

```env
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_production_key
NOTIFYRE_DEFAULT_NUMBER_PREFIX=+1
NOTIFYRE_BASE_URL=https://api.notifyre.com
NOTIFYRE_TIMEOUT=30
NOTIFYRE_RETRY_TIMES=3
NOTIFYRE_RETRY_SLEEP=1
NOTIFYRE_ROUTES_ENABLED=true
NOTIFYRE_ROUTE_PREFIX=notifyre
NOTIFYRE_RATE_LIMIT_ENABLED=true
NOTIFYRE_RATE_LIMIT_MAX=60
NOTIFYRE_RATE_LIMIT_WINDOW=1
NOTIFYRE_DB_ENABLED=true
NOTIFYRE_LOGGING_ENABLED=true
NOTIFYRE_LOG_PREFIX=notifyre_sms
NOTIFYRE_WEBHOOK_RETRY_ATTEMPTS=3
NOTIFYRE_WEBHOOK_RETRY_DELAY=1
```

### Testing

```env
NOTIFYRE_DRIVER=log
NOTIFYRE_LOGGING_ENABLED=true
```

## Validation

The package validates your configuration:

- **Driver**: Must be `sms` or `log`
- **API Key**: Required when using `sms` driver
- **Recipient Types**: Must be valid enum values (mobile_number, contact, group)
- **Recipient Values**: Must be valid phone numbers or contact/group identifiers

## Advanced Configuration

### Routes and Rate Limiting

Routes are enabled when `NOTIFYRE_ROUTES_ENABLED=true`. Prefix defaults to `notifyre`. Rate limiting is configurable via the `routes.rate_limit` section in `config/notifyre.php`.

### Database Persistence

Store SMS messages and recipients in your database:

```env
# Enable database storage
NOTIFYRE_DB_ENABLED=true
```

### Logging

Custom logging for Notifyre operations:

```env
# Enable custom logging
NOTIFYRE_LOGGING_ENABLED=true
```

## Response Handling

Both drivers handle responses differently:

- **SMS Driver**: Sends real SMS via Notifyre API and persists to database
- **Log Driver**: Logs messages to Laravel logs for testing

## Next Steps

- [Learn about drivers](./../technical/DRIVERS.md)
- [See usage examples](./../usage/DIRECT_SMS.md)
- [Set up notifications](./../usage/NOTIFICATIONS.md)
- [Explore API usage](./../usage/API.md)
