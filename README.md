# Notifyre Laravel Package

A clean, Laravel-native SMS package that integrates with Notifyre's SMS service. This package provides both direct SMS
sending (like Vonage) and full Laravel notification integration.

## Features

- ✅ **Direct SMS Sending** - Fast, simple SMS without Laravel notification overhead
- ✅ **Laravel Notifications** - Full notification system with queuing, events, and more
- ✅ **Multiple Drivers** - SMS driver for production, log driver for testing
- ✅ **Clean Architecture** - Separation of concerns with driver-based design
- ✅ **Error Handling** - Comprehensive exception handling and retry logic
- ✅ **CLI Support** - Send SMS directly from Artisan commands
- ✅ **Queue Support** - Built-in support for Laravel queues
- ✅ **Testing Ready** - Log driver for development and testing
- ✅ **Minimal Setup** - Only 2 environment variables required to get started

## Quick Start

### Installation

```bash
composer require arbi/notifyre-laravel
```

### Setup Options

#### Option 1: Minimal Setup (Recommended for quick start)

**Just add 2 environment variables to your `.env` file:**

```env
NOTIFYRE_DRIVER=log
NOTIFYRE_API_TOKEN=your_api_token_here
```

The package works with sensible defaults for everything else!

#### Option 2: Full Setup (Recommended for production)

Use our convenient artisan commands to set up everything:

```bash
# Publish everything at once (recommended)
php artisan notifyre:publish

# Or publish individually:
php artisan notifyre:publish-config    # Publish config file only
php artisan notifyre:publish-env       # Add env variables to .env
```

### Basic Usage

```php
// Direct SMS (fast, like Vonage)
notifyre()->send(new RequestBodyDTO(
    body: 'Hello World!',
    sender: 'MyApp',
    recipients: [
        new Recipient('mobile_number', '+1234567890'),
    ]
));

// Laravel notifications (full features)
$user->notify(new WelcomeNotification());
```

The package currently supports one recipient type:
- **`mobile_number`** - Direct phone numbers

### Test Your Installation

```bash
# Test SMS sending (logs to Laravel logs in development)
php artisan sms:send "TestApp" "+1234567890" "Hello from Notifyre!"

# Or use defaults from config
php artisan sms:send "" "" "Test message"
```

## Documentation

- **[Installation Guide](./INSTALLATION.md)** - How to install and set up the package
- **[Configuration Guide](./CONFIGURATION.md)** - All configuration options and environment variables
- **[Usage Guide](./USAGE.md)** - How to use the package in your application
- **[Drivers Guide](./DRIVERS.md)** - Detailed information about SMS and Log drivers
- **[Examples](./EXAMPLES.md)** - Real-world examples and best practices

## Architecture

The package follows clean architecture principles:

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

## Two Ways to Send SMS

### 1. Direct SMS (Fast)

```php
notifyre()->send($message);
// Goes directly to driver (SMS/Log)
// Bypasses Laravel notifications
// Perfect for simple, one-off SMS
```

### 2. Laravel Notifications (Full Features)

```php
$user->notify(new MyNotification());
// Goes through notification system
// Supports queuing, events, etc.
// Best for complex notification logic
```

## Drivers

- **`sms`** - Sends real SMS via Notifyre API
- **`log`** - Logs SMS to Laravel logs (for testing)

## Requirements

- PHP 8.4+
- Laravel 12.20+
- Notifyre API account

## License

MIT License - see [LICENSE.md](../LICENSE.md) for details.

## Contributing

See [CONTRIBUTING.md](../CONTRIBUTING.md) for contribution guidelines.

## Support

For issues and questions:

1. Check the documentation above
2. Review the examples
3. Open an issue on GitHub

---

**Built with ❤️ for the Laravel community**
