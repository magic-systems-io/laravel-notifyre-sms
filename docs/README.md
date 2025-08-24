# Notifyre Laravel Package - Documentation

Simple, focused documentation for the Notifyre Laravel package.

## 📚 What This Package Does

This package provides **two ways to send SMS**:

1. **Direct SMS** - Send SMS immediately using the `notifyre()` helper
2. **Laravel Notifications** - Send SMS through Laravel's notification system

## 📖 Documentation

### 🚀 Getting Started

- **[Installation](./getting-started/INSTALLATION.md)** - How to install and configure the package
- **[Configuration](./getting-started/CONFIGURATION.md)** - Environment variables and config options

### 💡 How to Use

- **[Direct SMS](./usage/DIRECT_SMS.md)** - Send SMS immediately using the helper function
- **[Notifications](./usage/NOTIFICATIONS.md)** - Send SMS through Laravel notifications
- **[Commands](./usage/COMMANDS.md)** - Send SMS from the command line

### 🔧 Technical Details

- **[Drivers](./technical/DRIVERS.md)** - How SMS and Log drivers work
- **[Architecture](./technical/ARCHITECTURE.md)** - Package structure and design
- **[Testing](./technical/TESTS.md)** - Testing strategies and examples

## 🚀 Quick Start

```bash
# Install
composer require magicsystems-io/notifyre-laravel

# Configure (minimal)
NOTIFYRE_DRIVER=log
NOTIFYRE_API_TOKEN=your_token

# Basic SMS
notifyre()->send(new RequestBodyDTO(
    body: 'Hello World!',
    recipients: [new Recipient('virtual_mobile_number', '+1234567890')]
));

# Advanced SMS with metadata and scheduling
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
```

## 🎯 What You Need to Know

- **2 environment variables** to get started
- **1 helper function** for direct SMS
- **1 notification channel** for Laravel notifications
- **2 drivers**: `sms` (production) and `log` (testing)
- **Rich DTOs** with Arrayable interface for easy data manipulation
- **Advanced features** like scheduling, callbacks, metadata, and campaign tracking

## 📁 Documentation Structure

```
docs/
├── README.md                    # This overview
├── getting-started/            # Setup and configuration
│   ├── INSTALLATION.md        # How to install
│   └── CONFIGURATION.md       # Configuration options
├── usage/                      # How to use the package
│   ├── DIRECT_SMS.md          # Send SMS directly
│   ├── NOTIFICATIONS.md       # Laravel notifications
│   └── COMMANDS.md            # Command line usage
└── technical/                  # Advanced topics
    ├── DRIVERS.md             # How drivers work
    ├── ARCHITECTURE.md        # Package design
    └── TESTS.md               # Testing strategies
```
