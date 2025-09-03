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

## Step 2: Configure Environment Variables

Add these to your `.env` file:

```env
# Required: Choose your driver
NOTIFYRE_DRIVER=log

# Required for SMS driver (production)
NOTIFYRE_API_KEY=your_api_key_here
```

## Step 3: Publish Configuration (Optional)

Publish the configuration file to customize settings:

```bash
php artisan vendor:publish --provider="MagicSystemsIO\Notifyre\Providers\NotifyreServiceProvider"
```

## Step 4: Run Migrations (Optional)

If you want to use the database persistence features:

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

Messages are logged to `storage/logs/laravel.log` instead of being sent. The log driver returns a mock response for testing
purposes.

### Production

```env
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_actual_api_key
```

Messages are sent through the Notifyre API and return real response data with delivery status.

## What Gets Installed

- **Service Providers**: Automatically registered
- **Facade**: `Notifyre` facade available
- **Helper Function**: `notifyre()` function available
- **Commands**: `sms:send` command available
- **Configuration**: `config/notifyre.php` available
- **DTOs**: Rich data transfer objects with Arrayable interface
- **Database Models**: SMS messages and recipients storage
- **HTTP Controllers**: REST API endpoints
- **Migrations**: Database schema for persistence

## Verify Installation

Check that the package is working:

```php
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;

// In tinker or a test
$response = notifyre()->send(new RequestBody(
    body: 'Test message',
    recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+1234567890')]
));

// Check response
if ($response && $response->success) {
    echo "Message sent successfully!";
    echo "Message ID: " . $response->payload->smsMessageID;
} else if ($response && !$response->success) {
    echo "Message failed: " . $response->message;
} else {
    echo "Message logged successfully (check Laravel logs)";
}
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
tail -f storage/logs/laravel.log
```

## Next Steps

- Learn about [Direct SMS usage](../usage/DIRECT_SMS.md)
- Explore [Laravel notifications](../usage/NOTIFICATIONS.md)
- Check out [CLI commands](../usage/COMMANDS.md)
- Review [Configuration options](./CONFIGURATION.md)
