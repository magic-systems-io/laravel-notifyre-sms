# Notifyre Laravel Package - Installation

## Installation

### Via Composer

```bash
composer require arbi/notifyre-laravel
```

## Setup Options

### Option 1: Minimal Setup (Quick Start)

**Perfect for getting started quickly or simple applications.**

The package comes with sensible defaults, so you only need to add **2 essential environment variables** to your `.env`
file:

```env
NOTIFYRE_DRIVER=log
NOTIFYRE_API_TOKEN=your_api_token_here
```

**That's it!** The package will work with all other default values.

#### What Gets Set Automatically

- **API Settings**: Base URL, timeout, retry logic
- **Rate Limiting**: SMS delays and limits
- **Caching**: Cache settings and TTL
- **Defaults**: Sender names, number prefixes
- **Testing**: Log driver enabled by default

#### Production Setup

When you're ready for production, just change:

```env
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_TOKEN=your_actual_api_key
```

### Option 2: Full Setup (Production Ready)

**Recommended for production applications or when you need full control.**

Use our convenient artisan commands to set up everything:

```bash
# Publish everything at once (recommended)
php artisan notifyre:publish

# Or publish individually:
php artisan notifyre:publish-config    # Publish config file only
php artisan notifyre:publish-env       # Add env variables to .env
```

#### Manual Configuration Publishing

If you prefer the traditional way:

```bash
php artisan vendor:publish --provider="Arbi\Notifyre\Providers\NotifyreServiceProvider"
```

This will publish the configuration file to `config/notifyre.php`.

## Complete Environment Variables

The `notifyre:publish-env` command will automatically add these variables to your `.env` file:

```env
NOTIFYRE_API_TOKEN=your_api_token_here
NOTIFYRE_DRIVER=log  # or 'sms' for production
NOTIFYRE_TIMEOUT=30
NOTIFYRE_RETRY_TIMES=3
NOTIFYRE_RETRY_SLEEP=1000
NOTIFYRE_BASE_URL=https://api.notifyre.com
NOTIFYRE_SMS_SENDER=YourAppName
NOTIFYRE_SMS_RECIPIENT=+1234567890
NOTIFYRE_DEFAULT_NUMBER_PREFIX=+1
NOTIFYRE_SMS_DELAY=1
NOTIFYRE_MAX_PER_MINUTE=60
NOTIFYRE_CACHE_ENABLED=true
NOTIFYRE_CACHE_TTL=3600
NOTIFYRE_CACHE_PREFIX=notifyre_
```

### Configuration Options

- **`NOTIFYRE_DRIVER`**: Choose between `sms` (send real SMS) or `log` (log to Laravel logs for testing)
- **`NOTIFYRE_API_TOKEN`**: Your Notifyre API key from the dashboard
- **`NOTIFYRE_BASE_URL`**: Notifyre API base URL (defaults to production)
- **`NOTIFYRE_TIMEOUT`**: HTTP request timeout in seconds
- **`NOTIFYRE_RETRY_TIMES`**: Number of retry attempts for failed requests
- **`NOTIFYRE_RETRY_SLEEP`**: Milliseconds between retry attempts

## Quick Setup Comparison

| Setup Type  | Commands                                | Environment Variables | Config File  | Use Case                 |
|-------------|-----------------------------------------|-----------------------|--------------|--------------------------|
| **Minimal** | `composer require`                      | 2 variables           | Default      | Quick start, testing     |
| **Full**    | `composer require` + `notifyre:publish` | All variables         | Customizable | Production, full control |

## Testing Your Installation

Once configured, test the package with:

```bash
# Test SMS sending (will log to Laravel logs if driver=log)
php artisan sms:send "TestApp" "+1234567890" "Hello from Notifyre!"

# Or use defaults from config
php artisan sms:send "" "" "Test message"
```

## Next Steps

After installation, you can:

1. [Configure the package](./CONFIGURATION.md)
2. [Learn about drivers](./DRIVERS.md)
3. [Learn how to use it](./USAGE.md)
4. [See practical examples](./EXAMPLES.md)
