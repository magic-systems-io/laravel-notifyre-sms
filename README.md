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

### 2) Publish config

```bash
php artisan notifyre:publish
```

### 3) Configure `.env`

```env
# Driver: sms (real API) or log (testing)
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_api_key_here

# Optional defaults
NOTIFYRE_DEFAULT_NUMBER_PREFIX=+1
NOTIFYRE_BASE_URL=https://api.notifyre.com
NOTIFYRE_TIMEOUT=30
NOTIFYRE_RETRY_TIMES=3
NOTIFYRE_RETRY_SLEEP=1

# Routes (default prefix /api/notifyre)
NOTIFYRE_ROUTES_ENABLED=true
NOTIFYRE_ROUTE_PREFIX=notifyre
NOTIFYRE_RATE_LIMIT_ENABLED=true
NOTIFYRE_RATE_LIMIT_MAX=60
NOTIFYRE_RATE_LIMIT_WINDOW=1

# Persistence & logging
NOTIFYRE_DB_ENABLED=true
NOTIFYRE_LOGGING_ENABLED=true
NOTIFYRE_LOG_PREFIX=notifyre_sms

# Webhook retry behavior
NOTIFYRE_WEBHOOK_RETRY_ATTEMPTS=3
NOTIFYRE_WEBHOOK_RETRY_DELAY=1
```

### 4) Migrate (for persistence)

```bash
php artisan migrate
```

### 5) Send your first SMS

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
php artisan sms:send --message="Hello from Notifyre!" --recipient="+1234567890"
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

---

Built with ❤️ for the Laravel community

