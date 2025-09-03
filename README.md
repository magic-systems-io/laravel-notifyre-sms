# Notifyre Laravel Package

A comprehensive Laravel-native SMS package that integrates with Notifyre's SMS service. Send SMS directly, through
Laravel notifications, or via REST API with database persistence and advanced features.

[![Tests](https://github.com/magic-systems-io/laravel-notifyre-sms/workflows/Tests/badge.svg)](https://github.com/magic-systems-io/laravel-notifyre-sms/actions)
[![Code Coverage](https://img.shields.io/badge/coverage-62%25-yellow.svg)](https://github.com/magic-systems-io/laravel-notifyre-sms)

## ✨ Features

- 🚀 **Direct SMS Sending** - Fast, simple SMS without notification overhead
- 🔔 **Laravel Notifications** - Full notification system with queuing and events
- 🔧 **Multiple Drivers** - SMS driver for production, log driver for testing
- 🏗️ **Clean Architecture** - Driver-based design with separation of concerns
- 🛡️ **Error Handling** - Comprehensive exception handling and validation
- 📱 **CLI Support** - Send SMS directly from Artisan commands
- 🌐 **REST API** - Full HTTP API with rate limiting and authentication
- 💾 **Database Persistence** - Store SMS messages and recipients in database
- ⚡ **Caching Support** - Built-in caching for API responses
- 🧪 **Testing Ready** - Log driver for development and testing
- ⚙️ **Flexible Configuration** - Extensive configuration options
- 📊 **Advanced DTOs** - Rich data transfer objects with Arrayable interface
- 🏷️ **Recipient Types** - Support for virtual mobile numbers, contacts, and groups
- 📝 **Message Tracking** - Track SMS messages with unique IDs

## 🚀 Quick Start

### Installation

```bash
composer require magic-systems-io/laravel-notifyre-sms
```

### Basic Setup

```env
NOTIFYRE_DRIVER=log
NOTIFYRE_API_KEY=your_api_key_here
```

### Basic Usage

```php
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;

// Direct SMS (fast)
notifyre()->send(new RequestBody(
    body: 'Hello World!',
    recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+1234567890')]
));

// With sender
notifyre()->send(new RequestBody(
    body: 'Your order has been shipped!',
    recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+1234567890')],
    sender: '+1987654321'
));

// Laravel notifications (full features)
$user->notify(new WelcomeNotification());
```

### Test Your Installation

```bash
php artisan sms:send --message="Hello from Notifyre!" --recipient="+1234567890"
```

## 📚 Documentation

**[📖 Full Documentation](./docs/README.md)** - Complete documentation index and navigation

**Quick Links:**

- **[Installation](./docs/getting-started/INSTALLATION.md)** - How to install and configure the package
- **[Direct SMS](./docs/usage/DIRECT_SMS.md)** - Send SMS immediately using the helper function
- **[Notifications](./docs/usage/NOTIFICATIONS.md)** - Send SMS through Laravel notifications
- **[Commands](./docs/usage/COMMANDS.md)** - Send SMS from the command line
- **[API Usage](./docs/usage/API.md)** - Use the REST API endpoints

## 🏗️ Architecture

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│ NotifyreService │───▶│  DriverFactory   │───▶│  SMSDriver      │
│  (Direct SMS)   │    │                  │    │  LogDriver      │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                                │
                                ▼
                       ┌──────────────────┐
                       │ NotifyreChannel  │
                       │ (Notifications)  │
                       └──────────────────┘
                                │
                                ▼
                       ┌──────────────────┐
                       │ HTTP Controllers │
                       │ (REST API)       │
                       └──────────────────┘
                                │
                                ▼
                       ┌──────────────────┐
                       │ Database Models  │
                       │ (Persistence)    │
                       └──────────────────┘
```

## 🔧 Drivers

- **`sms`** - Sends real SMS via Notifyre API
- **`log`** - Logs SMS to Laravel logs (for testing)

## 🌐 API Endpoints

The package provides REST API endpoints for SMS operations:

- `POST /api/notifyre/sms` - Send SMS messages
- `GET /api/notifyre/sms` - List SMS messages (requires sender parameter)
- `GET /api/notifyre/sms/{id}` - Get specific SMS message

## 📋 Requirements

- PHP 8.3+
- Laravel 12.20+
- Notifyre API account

## 📄 License

MIT License - see [LICENSE.md](./LICENSE.md) for details.

## 🤝 Contributing

See [CONTRIBUTING.md](./CONTRIBUTING.md) for contribution guidelines.

## 🆘 Support

For issues and questions:

1. Check the [documentation](./docs/README.md)
2. Review the [examples](./docs/usage/DIRECT_SMS.md)
3. Open an issue on GitHub

---

**Built with ❤️ for the Laravel community**
