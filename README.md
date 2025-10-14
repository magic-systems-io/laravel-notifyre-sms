# Notifyre Laravel Package

A Laravel package for sending SMS messages through the Notifyre API. Provides direct SMS sending, Laravel notification
integration, CLI commands, and REST API endpoints with optional database persistence.

[![Tests](https://github.com/magic-systems-io/laravel-notifyre-sms/actions/workflows/tests.yml/badge.svg)](https://github.com/magic-systems-io/laravel-notifyre-sms/actions)
[![Coverage Status](https://codecov.io/gh/magic-systems-io/laravel-notifyre-sms/branch/main/graph/badge.svg)](https://codecov.io/gh/magic-systems-io/laravel-notifyre-sms)

## Jumpstart

### 1) Install

```bash
composer require magic-systems-io/laravel-notifyre-sms
```

### 2) (Optional) Publish config

```bash
php artisan notifyre:publish-config
```

### 3) Configure `.env`

````bash
php artisan notifyre:publish-env
````

Then edit your `.env` file:

```env
NOTIFYRE_API_KEY=your_api_key_here
NOTIFYRE_WEBHOOK_SECRET=your_webhook_secret_here
# NOTIFYRE_LOG_LEVEL=debug  # Optional: emergency|alert|critical|error|warning|notice|info|debug
```

### 4) (Optional) Publish migration

```bash
php artisan notifyre:publish-migration
```

### 5) Migrate (for persistence)

```bash
php artisan migrate
```

### 6) Send your first SMS

```php
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;

notifyre()->send(new RequestBody(
    body: 'Hello World!',
    recipients: [new Recipient(NotifyreRecipientTypes::MOBILE_NUMBER->value, '+1234567890')]
));
```

Or via Artisan:

```bash
php artisan sms:send --message "Hello from Notifyre!" --recipient "+1234567890"
```

## Endpoints (default prefix `/api/notifyre`)

- `POST /api/notifyre/sms` — Send SMS (201 on acceptance)
- `GET /api/notifyre/sms` — List local messages (requires sender on authenticated user)
- `GET /api/notifyre/sms/{id}` — Get local message
- `GET /api/notifyre/sms/notifyre` — List via Notifyre API (proxy)
- `GET /api/notifyre/sms/notifyre/{id}` — Get via Notifyre API (proxy)
- `POST /api/notifyre/sms/webhook` — Delivery callback handler

## Commands

- `php artisan sms:send` — Send SMS
- `php artisan sms:list` — List/filter SMS (see `--help`)
- `php artisan notifyre:publish*` — Publish config/env snippets

## Logging

The package creates a dedicated log channel (`notifyre`) that:
- Respects `APP_DEBUG` - defaults to `info` in production, `debug` in development
- Can be customized via `NOTIFYRE_LOG_LEVEL` in `.env`
- Falls back to your app's `LOG_LEVEL` if set
- Logs to `storage/logs/notifyre_sms.log` (or `.log` files based on your default channel config)

## Requirements

- PHP 8.3+
- Laravel 12.20+
- Notifyre API account (for `sms` driver)

## Docs

See the full documentation: [README](docs/README.md).

## License

MIT License — see [LICENSE](LICENSE.md).

## Contributing

See [CONTRIBUTING](CONTRIBUTING.md) .

## Support

- Read [docs](docs)
- Try examples in [usage](docs/usage)
- Open a GitHub issue if needed

## Troubleshooting

**Provider not auto-discovered?** If the package isn't working, manually register it in `bootstrap/providers.php`:

```php
return [
    // Other Service Providers...
    MagicSystemsIO\Notifyre\Providers\NotifyreServiceProvider::class,
];
```

---

Made with ❤️ by [Magic Systems](https://magicsystems.io)
