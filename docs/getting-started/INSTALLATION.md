# Installation

Get the Notifyre Laravel package up and running in your application.

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

# Optional: Default sender number
NOTIFYRE_SMS_SENDER=+1234567890

# Optional: Default recipient (for testing)
NOTIFYRE_SMS_RECIPIENT=+1234567890
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
php artisan sms:send --message="Hello from Notifyre!"
```

## Driver Options

### Development/Testing (Recommended to start)

```env
NOTIFYRE_DRIVER=log
```

Messages are logged to `storage/logs/laravel.log` instead of being sent. The log driver returns `null` for testing
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
    recipients: [new Recipient(NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER->value, '+1234567890')]
));

// Check response (log driver returns null, SMS driver returns ResponseBody)
if ($response && $response->success) {
    echo "Message sent successfully!";
    echo "Message ID: " . $response->payload->smsMessageID;
} else if ($response === null) {
    echo "Message logged successfully (check Laravel logs)";
} else {
    echo "Failed: " . $response->message;
}
```

## API Endpoints

The package automatically registers these API routes:

- `POST /api/notifyre/sms` - Send SMS messages
- `GET /api/notifyre/sms` - List SMS messages
- `GET /api/notifyre/sms/{id}` - Get specific SMS message

To enable the API, ensure these environment variables are set:

```env
NOTIFYRE_API_ENABLED=true
NOTIFYRE_DB_ENABLED=true
```

## Configuration Files

The package creates several configuration files:

- `config/notifyre.php` - Main package configuration
- Database migrations for SMS storage
- Service provider registrations

## Next Steps

- [Learn how to send SMS directly](./../usage/DIRECT_SMS.md)
- [Set up Laravel notifications](./../usage/NOTIFICATIONS.md)
- [Explore the REST API](./../usage/API.md)
- [Configure advanced options](./CONFIGURATION.md)
