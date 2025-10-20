# Installation

Get the Notifyre Laravel package up and running in your application.

## Requirements

- PHP 8.3+
- Laravel 12.20+
- Notifyre API account (for production use)

## Step 1: Install via Composer

```bash
composer require magic-systems-io/laravel-notifyre-sms
```

## Step 2: Publish Configuration

Publish all configuration files and environment variables:

```bash
php artisan notifyre:publish
```

This command will:

- Publish the configuration file to `config/notifyre.php`
- Add environment variables to your `.env` file

## Step 3: Configure Environment Variables

Add these variables to your `.env` file:

```env
# Required: Choose your driver
NOTIFYRE_DRIVER=log

# Required for SMS driver (production)
NOTIFYRE_API_KEY=your_api_token_here

# Required for webhooks
NOTIFYRE_WEBHOOK_SECRET=your_webhook_secret_here

# Optional: Log level (defaults to 'debug' in dev, 'info' in production)
# NOTIFYRE_LOG_LEVEL=debug  # emergency|alert|critical|error|warning|notice|info|debug
```

**Note:** Most configuration options are set in `config/notifyre.php`. Run `php artisan notifyre:publish-config` to
customize routes, timeouts, database, rate limiting, etc.

## Step 4: Run Migrations

Run the package migrations to set up database tables:

```bash
php artisan migrate
```

## Step 5: Test Installation

Send a test SMS using the command line:

```bash
php artisan sms:send --message="Hello from Notifyre!" --recipient="+1234567890"
```

## Driver Options

### Development/Testing (Recommended to start)

```env
NOTIFYRE_DRIVER=log
```

Messages are logged to `storage/logs/laravel.log` instead of being sent. The log driver returns a mock response for
testing
purposes.

### Production

```env
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_actual_api_key
```

Messages are sent through the Notifyre API and return real response data with delivery status.

## What Gets Installed

- **Service Providers**: Automatically registered via package auto-discovery
- **Helper Function**: `notifyre()` function available
- **Log Channel**: `notifyre` channel for package-specific logging
- **Commands**:
    - `sms:send` - Send SMS messages
    - `sms:list` - List SMS messages with filtering
    - `notifyre:publish` - Publish all configuration files
    - `notifyre:publish-config` - Publish configuration file
    - `notifyre:publish-env` - Add environment variables
- **Configuration**: `config/notifyre.php` available
- **DTOs**: Rich data transfer objects with Arrayable interface
- **Database Models**: SMS messages and recipients storage
- **HTTP Controllers**: REST API endpoints
- **Migrations**: Database schema for persistence
- **Routes**: API routes for SMS operations

## Verify Installation

Check that the package is working:

```php
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;

// In tinker or a test
notifyre()->send(new RequestBody(
    body: 'Test message',
    recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+1234567890')]
));

// For log driver, check Laravel logs
// For SMS driver, the message will be sent to Notifyre API
```

## Troubleshooting

### Common Issues

#### "Invalid Notifyre driver" Error

Make sure you have set the `NOTIFYRE_DRIVER` environment variable:

```env
NOTIFYRE_DRIVER=log  # for testing
# or
NOTIFYRE_DRIVER=sms  # for production
```

#### "Notifyre API key is not configured" Error

For production use, you need to set your API key:

```env
NOTIFYRE_API_KEY=your_actual_api_key_here
```

#### "Notifyre base URL is not configured" Error

The base URL should be automatically set, but you can override it:

```env
NOTIFYRE_BASE_URL=https://api.notifyre.com
```

### Check Configuration

Verify your configuration is correct:

```bash
php artisan config:show notifyre
```

### Test with Log Driver

Always test with the log driver first:

```bash
# Set log driver
export NOTIFYRE_DRIVER=log

# Test command
php artisan sms:send --message="Test message" --recipient="+1234567890"

# Check logs
tail -f storage/logs/notifyre_sms.log
# Or
tail -f storage/logs/laravel.log
```

### Provider Not Auto-Discovered

If the package isn't working, manually register it in `bootstrap/providers.php`:

```php
return [
    // Other Service Providers...
    MagicSystemsIO\Notifyre\Providers\NotifyreServiceProvider::class,
];
```

## Next Steps

- Learn about [Direct SMS usage](../usage/DIRECT_SMS.md)
- Explore [Laravel notifications](../usage/NOTIFICATIONS.md)
- Check out [CLI commands](../usage/COMMANDS.md)
- Review [Configuration options](./CONFIGURATION.md)
