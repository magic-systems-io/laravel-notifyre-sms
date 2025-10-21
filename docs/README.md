# Notifyre Laravel Package - Documentation

Complete documentation for the Notifyre Laravel package with all features and capabilities.

## What This Package Does

This package provides multiple ways to send SMS messages:

1. **Direct SMS** - Send SMS immediately using the `notifyre()` helper function
2. **Laravel Notifications** - Send SMS through Laravel's notification system via NotifyreChannel
3. **REST API** - Send SMS via HTTP endpoints with rate limiting and database persistence
4. **CLI Commands** - Send and manage SMS from Artisan commands

## Documentation

### Getting Started

- **[Installation](./getting-started/INSTALLATION.md)** - How to install and configure the package
- **[Configuration](./getting-started/CONFIGURATION.md)** - Environment variables and config options

### How to Use

- **[Direct SMS](./usage/DIRECT_SMS.md)** - Send SMS immediately using the helper function
- **[Notifications](./usage/NOTIFICATIONS.md)** - Send SMS through Laravel notifications
- **[Commands](./usage/COMMANDS.md)** - Send SMS from the command line
- **[API Usage](./usage/API.md)** - Use the REST API endpoints

### Technical Details

- **[Drivers](./technical/DRIVERS.md)** - How SMS and Log drivers work
- **[Architecture](./technical/ARCHITECTURE.md)** - Package structure and design
- **[Testing](./technical/TESTS.md)** - Testing strategies and examples

## Quick Start

```bash
# Install
composer require magic-systems-io/laravel-notifyre-sms

# Publish configuration
php artisan notifyre:publish

# Set environment variables
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_api_key
NOTIFYRE_DEFAULT_NUMBER_PREFIX=+1

# Optional HTTP & routes
NOTIFYRE_BASE_URL=https://api.notifyre.com
NOTIFYRE_TIMEOUT=30
NOTIFYRE_RETRY_TIMES=3
NOTIFYRE_RETRY_SLEEP=1
NOTIFYRE_ROUTES_ENABLED=true
NOTIFYRE_ROUTE_PREFIX=notifyre
```

```php
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;

notifyre()->send(new RequestBody(
    body: 'Hello World!',
    recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+1234567890')]
));
```

## Package Features

### Core Functionality

- **SMS Sending** - Direct SMS via `notifyre()` helper function
- **Notification Channel** - Laravel notification integration via NotifyreChannel
- **CLI Commands** - Artisan commands for SMS operations
- **Driver System** - SMS driver for production, log driver for testing

### Advanced Features

- **Database Models** - Store and retrieve SMS messages and recipients
- **HTTP Controllers** - REST API endpoints with rate limiting
- **Request Validation** - Comprehensive input validation
- **Error Handling** - Detailed error messages and exceptions
- **Configuration Management** - Extensive configuration options
- **Message Tracking** - Track SMS delivery status with callbacks
- **Recipient Types** - Support for mobile numbers, contacts, and groups
- **Delivery Status Tracking** - Track message delivery with status enums (sent, delivered, failed, etc.)

### Available Commands

- `php artisan sms:send` - Send SMS messages
- `php artisan sms:list` - List SMS messages with filtering options
- `php artisan notifyre:publish` - Publish all configuration files
- `php artisan notifyre:publish-config` - Publish configuration file
- `php artisan notifyre:publish-env` - Add environment variables to .env

### API Endpoints (default prefix `/api/notifyre`)

- `POST /api/notifyre/sms` - Send SMS messages
- `GET /api/notifyre/sms` - List local SMS messages (requires sender from authenticated user)
- `GET /api/notifyre/sms/{id}` - Get specific local SMS message
- `GET /api/notifyre/sms/notifyre` - List SMS via Notifyre API (proxy)
- `GET /api/notifyre/sms/notifyre/{id}` - Get SMS via Notifyre API (proxy)
- `POST /api/notifyre/sms/webhook` - Handle delivery callbacks

## Documentation Structure

```
docs/
├── README.md                    # This overview
├── getting-started/            # Setup and configuration
│   ├── INSTALLATION.md        # How to install
│   └── CONFIGURATION.md       # Configuration options
├── usage/                      # How to use the package
│   ├── DIRECT_SMS.md          # Send SMS directly
│   ├── NOTIFICATIONS.md       # Laravel notifications
│   ├── COMMANDS.md            # Command line usage
│   └── API.md                 # REST API usage
└── technical/                  # Advanced topics
    ├── DRIVERS.md             # How drivers work
    ├── ARCHITECTURE.md        # Package design
    └── TESTS.md               # Testing strategies
```

## Environment Variables

### Required

- `NOTIFYRE_DRIVER` - Driver type (sms/log)
- `NOTIFYRE_API_KEY` - Your Notifyre API key
- `NOTIFYRE_WEBHOOK_SECRET` - Webhook secret for delivery callbacks

### Optional

- `NOTIFYRE_USE_UUID` - Use UUIDs for junction table primary keys (default: true)
- `NOTIFYRE_LOG_LEVEL` - Log level for Notifyre (emergency|alert|critical|error|warning|notice|info|debug)

**Note:** Most configuration options (routes, timeouts, database, rate limiting, etc.) are set in `config/notifyre.php`.
Publish the config file to customize these settings.
