# Notifyre Laravel Package

A clean, Laravel-native SMS package that integrates with Notifyre's SMS service. Send SMS directly or through Laravel
notifications with minimal setup.

![Test Coverage](https://img.shields.io/badge/coverage-34.9%25-brightgreen?style=flat-square&logo=php)

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
- 📊 **Advanced DTOs** - Rich data transfer objects with Arrayable interface
- 🕒 **Scheduling Support** - Schedule SMS for future delivery
- 🔗 **Callback URLs** - Webhook support for delivery status
- 🏷️ **Metadata Support** - Add custom key-value pairs to messages
- 📝 **Campaign Tracking** - Optional campaign names for message organization

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
    recipients: [new Recipient('virtual_mobile_number', '+1234567890')]
));

// Advanced SMS with metadata and scheduling
notifyre()->send(new RequestBodyDTO(
    body: 'Your order has been shipped!',
    recipients: [new Recipient('virtual_mobile_number', '+1234567890')],
    from: '+1987654321',
    scheduledDate: time() + 3600, // Send in 1 hour
    addUnsubscribeLink: true,
    callbackUrl: 'https://yourapp.com/sms-callback',
    metadata: ['order_id' => '12345', 'customer_type' => 'premium'],
    campaignName: 'Order Shipping Campaign'
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
                       └─────────────────┘
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

1. Check the [documentation](./docs/README.md)
2. Review the [examples](./docs/usage/DIRECT_SMS.md)
3. Open an issue on GitHub

---

**Built with ❤️ for the Laravel community**
