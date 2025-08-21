# Installation

Get the Notifyre Laravel package up and running in your application.

## Step 1: Install via Composer

```bash
composer require arbi/notifyre-laravel
```

## Step 2: Configure Environment Variables

Add these to your `.env` file:

```env
# Required: Choose your driver
NOTIFYRE_DRIVER=log

# Required for SMS driver (production)
NOTIFYRE_API_KEY=your_api_key_here

# Optional: Default sender name
NOTIFYRE_SMS_SENDER=MyApp
```

## Step 3: Test Installation

Send a test SMS using the command line:

```bash
php artisan sms:send --message="Hello from Notifyre!"
```

## Driver Options

### Development/Testing (Recommended to start)

```env
NOTIFYRE_DRIVER=log
```

Messages are logged to `storage/logs/laravel.log` instead of being sent.

### Production

```env
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_actual_api_key
```

Messages are sent through the Notifyre API.

## What Gets Installed

- **Service Provider**: Automatically registered
- **Facade**: `Notifyre` facade available
- **Helper Function**: `notifyre()` function available
- **Commands**: `sms:send` command available
- **Configuration**: `config/notifyre.php` available

## Verify Installation

Check that the package is working:

```php
// In tinker or a test
notifyre()->send(new RequestBodyDTO(
    body: 'Test message',
    sender: 'TestApp',
    recipients: [new Recipient('mobile_number', '+1234567890')]
));
```

## Next Steps

- [Learn how to send SMS directly](./../usage/DIRECT_SMS.md)
- [Set up Laravel notifications](./../usage/NOTIFICATIONS.md)
- [Configure advanced options](./CONFIGURATION.md)
