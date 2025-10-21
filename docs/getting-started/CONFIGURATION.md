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
# Webhook secret for delivery callbacks
NOTIFYRE_WEBHOOK_SECRET=your_webhook_secret_here

# Use UUIDs for junction table primary keys (defaults to true)
# IMPORTANT: Set this BEFORE running migrations
NOTIFYRE_USE_UUID=true

# Log level (optional - defaults to 'debug' in dev, 'info' in production)
# NOTIFYRE_LOG_LEVEL=debug  # emergency|alert|critical|error|warning|notice|info|debug
```

**Note:** Most configuration options are set in `config/notifyre.php` and require publishing the config file to
customize. Only the driver, API key, webhook secret, UUID mode, and log level can be set via environment variables.

## Configuration File

The package creates `config/notifyre.php` with these sections:

### Driver Settings

```php
'driver' => env('NOTIFYRE_DRIVER', 'sms'),
'api_key' => env('NOTIFYRE_API_KEY'),
```

### Default Settings

```php
'default_number_prefix' => '+1',
```

**Note:** This is hardcoded in the config file. To change it, publish and edit `config/notifyre.php`.

### HTTP Configuration

```php
'http' => [
    'base_url' => 'https://api.notifyre.com',
    'timeout' => 30, // seconds
    'retry' => [
        'times' => 3,
        'sleep' => 1, // seconds between retries
    ],
],
```

**Note:** These are hardcoded. To change them, publish and edit `config/notifyre.php`.

### Routes Configuration

```php
'routes' => [
    'enabled' => true,
    'prefix' => 'notifyre',
    'middleware' => [], // Add middleware as needed (e.g., ['api'], ['auth:sanctum'])
    'rate_limit' => [
        'enabled' => true,
        'max_requests' => 60, // per minute
        'decay_minutes' => 1,
    ],
],
```

**Note:** By default, routes have no middleware to ensure webhook endpoints work out of the box. Add middleware as needed for your authenticated routes. To change these settings, publish and edit `config/notifyre.php`.

### Database Configuration

```php
'database' => [
    'enabled' => true,
    'use_uuid' => env('NOTIFYRE_USE_UUID', true),
],
```

**Use UUID Mode:**
- When `true` (default): Junction table uses UUID for its primary key
- When `false`: Junction table uses auto-incrementing integer for its primary key
- Note: Messages and recipients tables always use string IDs from Notifyre API

**IMPORTANT:** Set `NOTIFYRE_USE_UUID` in your `.env` file BEFORE running migrations. The migration reads this config value when it runs to determine the database schema. Changing it after migrations have been run will cause inconsistencies between your schema and application logic.

**Note:** Database persistence is hardcoded as enabled. To disable it, publish and edit `config/notifyre.php`.

### Logging Configuration

```php
'logging' => [
    'enabled' => true,
    'prefix' => 'notifyre_sms',
    'level' => env('NOTIFYRE_LOG_LEVEL', env('LOG_LEVEL', 'debug')),
],
```

The log level can be customized via `NOTIFYRE_LOG_LEVEL` environment variable:

- Falls back to `LOG_LEVEL`, then `debug`
- Automatically uses `info` in production when `APP_DEBUG=false` and no explicit level is set
- Logs to `storage/logs/notifyre_sms.log`

**Note:** To disable logging or change the prefix, publish and edit `config/notifyre.php`.

### Webhook Configuration

```php
'webhook' => [
    'secret' => env('NOTIFYRE_WEBHOOK_SECRET'),
    'retry_attempts' => 3,
    'retry_delay' => 1, // seconds between retries
    'signature_tolerance' => 300, // 5 minutes
],
```

The webhook system provides:

- **Signature Verification**: HMAC-SHA256 signature verification using webhook secret
- **Delivery Status Tracking**: Uses `NotifyProcessedStatus` enum to track message delivery
- **Retry Logic**: Configurable retry attempts for message lookup
- **Idempotency**: Prevents duplicate webhook processing
- **Timestamp Validation**: Rejects webhooks outside the tolerance window (default 5 minutes)

**Note:** Only the webhook secret can be set via `NOTIFYRE_WEBHOOK_SECRET`. To change retry settings or signature tolerance, publish and edit `config/notifyre.php`.

## Driver-Specific Configuration

### SMS Driver

```env
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_api_key
NOTIFYRE_WEBHOOK_SECRET=your_webhook_secret
```

### Log Driver

```env
NOTIFYRE_DRIVER=log
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
NOTIFYRE_WEBHOOK_SECRET=your_webhook_secret
# NOTIFYRE_LOG_LEVEL=info  # Optional: Set to 'info' for production
```

**Note:** Most settings are configured in `config/notifyre.php`. Publish the config file to customize routes, timeouts,
database, etc.

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

Routes are enabled by default with prefix `notifyre`. To customize routes or rate limiting:

1. Publish the config: `php artisan notifyre:publish-config`
2. Edit the `routes` section in `config/notifyre.php`

### Database Persistence

Database persistence is enabled by default in `config/notifyre.php`. To disable it:

1. Publish the config: `php artisan notifyre:publish-config`
2. Edit `config/notifyre.php` and set `'database' => ['enabled' => false]`

### Logging

Custom logging for Notifyre operations:

```env
# Enable custom logging
NOTIFYRE_LOGGING_ENABLED=true

# Set log level (optional)
NOTIFYRE_LOG_LEVEL=info  # emergency|alert|critical|error|warning|notice|info|debug
```

The logging system:

- Respects `APP_DEBUG` - defaults to `info` in production, `debug` in development
- Falls back to your app's `LOG_LEVEL` if set
- Can be customized per-package via `NOTIFYRE_LOG_LEVEL`

## Response Handling

Both drivers handle responses differently:

- **SMS Driver**: Sends real SMS via Notifyre API and persists to database
- **Log Driver**: Logs messages to Laravel logs for testing

## Next Steps

- [Learn about drivers](./../technical/DRIVERS.md)
- [See usage examples](./../usage/DIRECT_SMS.md)
- [Set up notifications](./../usage/NOTIFICATIONS.md)
- [Explore API usage](./../usage/API.md)
