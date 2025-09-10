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

# Feature toggles
NOTIFYRE_API_ENABLED=true
NOTIFYRE_DB_ENABLED=true
NOTIFYRE_LOGGING_ENABLED=true
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

### API Settings

```php
'base_url' => env('NOTIFYRE_BASE_URL', 'https://api.notifyre.com'),
'timeout' => 30,
'retry' => [
    'times' => 3,
    'sleep' => 1000, // milliseconds between retries
],
```

### API Configuration

```php
'api' => [
    'enabled' => env('NOTIFYRE_API_ENABLED', true),
    'prefix' => 'notifyre',
    'middleware' => 'api',
    'rate_limit' => [
        'enabled' => true,
        'max_requests' => 60, // Maximum requests per minute
        'decay_minutes' => 1, // Time window for rate limiting in minutes
    ],
    'database' => [
        'enabled' => env('NOTIFYRE_DB_ENABLED', true),
    ],
],
```

### Logging Configuration

```php
'logging' => [
    'prefix' => 'notifyre_sms',
    'enabled' => env('NOTIFYRE_LOGGING_ENABLED', true),
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
NOTIFYRE_BASE_URL=https://api.notifyre.com
NOTIFYRE_API_ENABLED=true
NOTIFYRE_DB_ENABLED=true
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

### API Features

The package provides REST API functionality:

```env
# Enable REST API endpoints
NOTIFYRE_API_ENABLED=true

# Database persistence
NOTIFYRE_DB_ENABLED=true
```

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
