# Notifyre Laravel Package - Documentation

Comprehensive documentation for the Notifyre Laravel package with all its features and capabilities.

## 📚 What This Package Does

This package provides **three ways to send SMS**:

1. **Direct SMS** - Send SMS immediately using the `notifyre()` helper
2. **Laravel Notifications** - Send SMS through Laravel's notification system
3. **REST API** - Send SMS via HTTP endpoints with full CRUD operations

## 📖 Documentation

### 🚀 Getting Started

- **[Installation](./getting-started/INSTALLATION.md)** - How to install and configure the package
- **[Configuration](./getting-started/CONFIGURATION.md)** - Environment variables and config options

### 💡 How to Use

- **[Direct SMS](./usage/DIRECT_SMS.md)** - Send SMS immediately using the helper function
- **[Notifications](./usage/NOTIFICATIONS.md)** - Send SMS through Laravel notifications
- **[Commands](./usage/COMMANDS.md)** - Send SMS from the command line
- **[API Usage](./usage/API.md)** - Use the REST API endpoints

### 🔧 Technical Details

- **[Drivers](./technical/DRIVERS.md)** - How SMS and Log drivers work
- **[Architecture](./technical/ARCHITECTURE.md)** - Package structure and design
- **[Testing](./technical/TESTS.md)** - Testing strategies and examples

## 🚀 Quick Start

```bash
# Install
composer require magic-systems-io/laravel-notifyre-sms

# Configure (basic setup)
NOTIFYRE_DRIVER=log
NOTIFYRE_API_KEY=your_api_key

# Basic SMS
notifyre()->send(new RequestBodyDTO(
    body: 'Hello World!',
    recipients: [new Recipient('mobile_number', '+1234567890')]
));

# With sender
notifyre()->send(new RequestBodyDTO(
    body: 'Your order has been shipped!',
    recipients: [new Recipient('mobile_number', '+1234567890')],
    sender: '+1987654321'
));
```

## 🎯 What You Need to Know

- **Multiple environment variables** for comprehensive configuration
- **1 helper function** for direct SMS
- **1 notification channel** for Laravel notifications
- **2 drivers**: `sms` (production) and `log` (testing)
- **Rich DTOs** with Arrayable interface for easy data manipulation
- **Database persistence** for storing SMS messages and recipients
- **REST API** with rate limiting and authentication
- **Caching support** for improved performance
- **Recipient types** including virtual mobile numbers, contacts, and groups

## 🏗️ Package Features

### Core Functionality

- **SMS Sending** - Direct SMS via helper function
- **Notification Channel** - Laravel notification integration
- **CLI Commands** - Artisan commands for SMS operations
- **Driver System** - Pluggable SMS and logging drivers

### Advanced Features

- **Database Models** - Store and retrieve SMS messages
- **HTTP Controllers** - REST API endpoints
- **Request Validation** - Comprehensive input validation
- **Error Handling** - Detailed error messages and exceptions
- **Configuration Management** - Extensive configuration options
- **Rate Limiting** - Built-in API rate limiting
- **Caching** - Response caching for performance

### Configuration Options

- **Driver Selection** - Choose between SMS and log drivers
- **API Settings** - Base URL, timeout, retry logic
- **Database Options** - Enable/disable persistence
- **Cache Settings** - TTL and prefix configuration
- **Rate Limiting** - Request limits and decay windows

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
│   ├── COMMANDS.md            # Command line usage
│   └── API.md                 # REST API usage
└── technical/                  # Advanced topics
    ├── DRIVERS.md             # How drivers work
    ├── ARCHITECTURE.md        # Package design
    └── TESTS.md               # Testing strategies
```

## 🔧 Environment Variables

### Required

- `NOTIFYRE_DRIVER` - Driver type (sms/log)
- `NOTIFYRE_API_KEY` - Your Notifyre API key

### Optional

- `NOTIFYRE_DEFAULT_NUMBER_PREFIX` - Country code prefix
- `NOTIFYRE_BASE_URL` - API base URL
- `NOTIFYRE_TIMEOUT` - HTTP timeout
- `NOTIFYRE_RETRY_TIMES` - Retry attempts
- `NOTIFYRE_RETRY_SLEEP` - Retry delay

### API Configuration

- `NOTIFYRE_API_ENABLED` - Enable/disable API
- `NOTIFYRE_API_PREFIX` - API route prefix
- `NOTIFYRE_RATE_LIMIT_ENABLED` - Enable rate limiting
- `NOTIFYRE_DB_ENABLED` - Enable database persistence
- `NOTIFYRE_CACHE_ENABLED` - Enable response caching
