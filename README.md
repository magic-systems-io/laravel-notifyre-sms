# Notifyre Laravel Package

A clean, Laravel-native SMS package that integrates with Notifyre's SMS service. Send SMS directly or through Laravel notifications with minimal setup.

![Test Coverage](https://img.shields.io/badge/coverage-64.0%25-brightgreen?style=flat-square&logo=php)

## ✨ Features

- 🚀 **Direct SMS Sending** - Fast, simple SMS without notification overhead
- 🔔 **Laravel Notifications** - Full notification system with queuing and events
- 🔧 **Multiple Drivers** - SMS driver for production, log driver for testing
- 🏗️ **Clean Architecture** - Driver-based design with separation of concerns
- 🛡️ **Error Handling** - Comprehensive exception handling and retry logic
- 📱 **CLI Support** - Send SMS directly from Artisan commands
- ⚡ **Queue Support** - Built-in support for Laravel queues
- 🧪 **Testing Ready** - Log driver for development and testing
- ⚙️ **Minimal Setup** - Only 2 environment variables required to get started

## 🚀 Quick Start

### Installation

```bash
composer require arbi/notifyre-laravel
```

### Minimal Setup (2 environment variables)

```env
NOTIFYRE_DRIVER=log
NOTIFYRE_API_TOKEN=your_api_token_here
```

### Basic Usage

```php
// Direct SMS (fast)
notifyre()->send(new RequestBodyDTO(
    body: 'Hello World!',
    sender: 'MyApp',
    recipients: [new Recipient('mobile_number', '+1234567890')]
));

// Laravel notifications (full features)
$user->notify(new WelcomeNotification());
```

### Test Your Installation

```bash
php artisan sms:send --message="Hello from Notifyre!"
```

## 📚 Documentation

**[📖 Full Documentation](./docs/README.md)** - Complete documentation index and navigation

**Quick Links:**
- **[Installation](./docs/getting-started/INSTALLATION.md)** - How to install and configure the package
- **[Direct SMS](./docs/usage/DIRECT_SMS.md)** - Send SMS immediately using the helper function
- **[Notifications](./docs/usage/NOTIFICATIONS.md)** - Send SMS through Laravel notifications
- **[Commands](./docs/usage/COMMANDS.md)** - Send SMS from the command line

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
```

## 🔧 Drivers

- **`sms`** - Sends real SMS via Notifyre API
- **`log`** - Logs SMS to Laravel logs (for testing)

## 📋 Requirements

- PHP 8.4+
- Laravel 12.20+
- Notifyre API account

## 📄 License

MIT License - see [LICENSE.md](./LICENSE.md) for details.

## 🤝 Contributing

See [CONTRIBUTING.md](./CONTRIBUTING.md) for contribution guidelines.

## 🆘 Support

For issues and questions:

1. Check the [documentation](./docs/)
2. Review the [examples](./docs/usage/DIRECT_SMS.md)
3. Open an issue on GitHub

---

**Built with ❤️ for the Laravel community**
