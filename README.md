# Notifyre Laravel Package

A Laravel package for sending SMS messages through the Notifyre API. Provides direct SMS sending, Laravel notification integration, and REST API endpoints with database persistence.

[![Tests](https://github.com/magic-systems-io/laravel-notifyre-sms/actions/workflows/tests.yml/badge.svg)](https://github.com/magic-systems-io/laravel-notifyre-sms/actions)

## Features

- **Direct SMS Sending** - Send SMS messages using the `notifyre()` helper function
- **Laravel Notifications** - Integration with Laravel's notification system via NotifyreChannel
- **Driver System** - SMS driver for production, log driver for testing
- **CLI Commands** - Send and list SMS messages from Artisan commands
- **REST API** - HTTP endpoints for SMS operations with rate limiting
- **Database Persistence** - Store SMS messages and recipients in database
- **Recipient Types** - Support for mobile numbers, contacts, and groups
- **Message Tracking** - Track SMS delivery status with callbacks
- **Configuration Management** - Comprehensive configuration options
- **Error Handling** - Detailed error messages and validation

## Quick Start

### Installation

```bash
composer require magic-systems-io/laravel-notifyre-sms
```

### Configuration

Publish configuration and environment variables:

```bash
php artisan notifyre:publish
```

Set your environment variables in `.env`:

```env
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_api_key_here
NOTIFYRE_DEFAULT_NUMBER_PREFIX=+1
```

### Basic Usage

```php
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;

// Direct SMS
notifyre()->send(new RequestBody(
    body: 'Hello World!',
    recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+1234567890')]
));

// With sender and additional options
notifyre()->send(new RequestBody(
    body: 'Your order has been shipped!',
    recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+1234567890')],
    sender: '+1987654321',
    scheduledDate: time() + 3600, // Schedule for 1 hour from now
    addUnsubscribeLink: true
));
```

### Test Your Installation

```bash
php artisan sms:send --message="Hello from Notifyre!" --recipient="+1234567890"
```

## Documentation

**[Full Documentation](./docs/README.md)** - Complete documentation index and navigation

**Quick Links:**

- **[Installation](./docs/getting-started/INSTALLATION.md)** - How to install and configure the package
- **[Direct SMS](./docs/usage/DIRECT_SMS.md)** - Send SMS immediately using the helper function
- **[Notifications](./docs/usage/NOTIFICATIONS.md)** - Send SMS through Laravel notifications
- **[Commands](./docs/usage/COMMANDS.md)** - Send SMS from the command line
- **[API Usage](./docs/usage/API.md)** - Use the REST API endpoints

## Architecture

```
NotifyreService (Direct SMS)
    ↓
SmsDriver (Production)
    ↓
NotifyreChannel (Notifications)
    ↓
NotifyreSmsController (REST API)
    ↓
Database Models (Persistence)
```

## Drivers

- **`sms`** - Sends real SMS via Notifyre API
- **`log`** - Logs SMS to Laravel logs (for testing)

## API Endpoints

The package provides REST API endpoints for SMS operations:

- `POST /api/notifyre/sms` - Send SMS messages
- `GET /api/notifyre/sms` - List SMS messages (requires sender parameter)
- `GET /api/notifyre/sms/{id}` - Get specific SMS message
- `GET /api/notifyre/sms/list-api` - List SMS via Notifyre API
- `GET /api/notifyre/sms/api/{id}` - Get SMS via Notifyre API
- `POST /api/notifyre/callback/sms` - Handle delivery callbacks

## Commands

- `php artisan sms:send` - Send SMS messages
- `php artisan sms:list` - List SMS messages with filtering options
- `php artisan notifyre:publish` - Publish all configuration files
- `php artisan notifyre:publish-config` - Publish configuration file
- `php artisan notifyre:publish-env` - Add environment variables to .env

## Requirements

- PHP 8.3+
- Laravel 12.20+
- Notifyre API account

## License

MIT License - see [LICENSE.md](./LICENSE.md) for details.

## Contributing

See [CONTRIBUTING.md](./CONTRIBUTING.md) for contribution guidelines.

## Support

For issues and questions:

1. Check the [documentation](./docs/README.md)
2. Review the [examples](./docs/usage/DIRECT_SMS.md)
3. Open an issue on GitHub

---

**Built with ❤️ for the Laravel community**

