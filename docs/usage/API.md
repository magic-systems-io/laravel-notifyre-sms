# REST API Usage

The Notifyre Laravel package provides REST API endpoints for SMS operations, including sending messages and retrieving
message history.

## Quick Start

### Enable Routes

Ensure routes are enabled in your configuration:

```env
NOTIFYRE_ROUTES_ENABLED=true
```

### Base Prefix

Routes are registered under the prefix configured by `NOTIFYRE_ROUTE_PREFIX` (default `notifyre`). The examples below
assume the default prefix and the default `api` middleware group, so the full base path is `/api/notifyre`.

### Routes

The package registers these API routes:

- `GET /api/notifyre/sms/notifyre` - List SMS via Notifyre API (proxy)
- `GET /api/notifyre/sms/notifyre/{id}` - Get SMS via Notifyre API (proxy)
- `GET /api/notifyre/sms` - List local SMS messages for the current sender
- `POST /api/notifyre/sms` - Send SMS message
- `GET /api/notifyre/sms/{id}` - Get a specific local SMS message
- `GET /api/notifyre/recipient/{recipient}` - Show messages sent to a recipient
- `POST /api/notifyre/sms/webhook` - Handle delivery callbacks

Note: Local routes are available only when database features are enabled (`NOTIFYRE_DB_ENABLED=true`).

## Sending SMS

### Endpoint

```
POST /api/notifyre/sms
```

### Request Body

```json
{
  "body": "Hello from the API!",
  "recipients": [
    { "type": "mobile_number", "value": "+1234567890" }
  ],
  "sender": "+1987654321"
}
```

- `recipients[].type` defaults to `mobile_number` if omitted.

### Response

Status 201

```json
"Message is being sent"
```

### Error Response

Status 500

```json
{ "message": "Failed to send SMS" }
```

## Retrieving Messages

### List Local Messages

Requires the current authenticated user's sender to be available via `Request::user()->getSender()`.

```
GET /api/notifyre/sms
```

#### Failure when sender is missing

Status 422

```json
{ "error": "Sender parameter is required" }
```

#### Success (paginated shape)

```json
{
  "data": [
    {
      "id": 1,
      "messageId": "sms-123",
      "sender": "+1987654321",
      "body": "Hello from the API!",
      "driver": "sms",
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    }
  ],
  "current_page": 1,
  "per_page": 15,
  "total": 1
}
```

### Get Specific Local Message

```
GET /api/notifyre/sms/{id}
```

- 404 when message is not found.

### Show Messages Sent To a Recipient

```
GET /api/notifyre/recipient/{recipient}
```

- 404 when recipient is not found.

## Notifyre API Proxies

### List via Notifyre API

```
GET /api/notifyre/sms/notifyre
```

### Get via Notifyre API

```
GET /api/notifyre/sms/notifyre/{id}
```

Both endpoints proxy to the Notifyre API using your configured credentials and return the upstream response. Failures
return status 500 with `{ "message": string }`.

## Webhook

```
POST /api/notifyre/sms/webhook
```

The webhook endpoint handles delivery status callbacks from Notifyre:

### Features

- **Signature Verification**: HMAC-SHA256 signature verification for security
- **Status Tracking**: Uses `NotifyPreprocessedStatus` enum to determine delivery success
- **Retry Logic**: Retries message lookup for up to `NOTIFYRE_WEBHOOK_RETRY_ATTEMPTS` attempts
- **Idempotency**: Prevents duplicate processing of the same webhook
- **Database Updates**: Updates recipient ID and sent status atomically

### Delivery Status

The webhook uses the `NotifyPreprocessedStatus` enum to determine if a message was successfully sent:

- **Successful**: `sent`, `delivered`
- **Unsuccessful**: `queued`, `failed`, `pending`, `undelivered`

### Responses

- **200**: Webhook processed successfully
- **200**: Webhook already processed (idempotent)
- **404**: Message not found after all retry attempts
- **404**: Recipient not found
- **401**: Signature verification failed (invalid or missing signature)
- **500**: Processing error

### Security

Webhook requests are verified using the `Notifyre-Signature` header. Configure your webhook secret in the environment:

```env
NOTIFYRE_WEBHOOK_SECRET=your_webhook_secret_here
```

## Authentication and Middleware

- Routes use the `api` middleware by default; add auth middleware via `notifyre.routes.middleware` in
  `config/notifyre.php` if needed.
- A throttle is applied when `NOTIFYRE_RATE_LIMIT_ENABLED=true`.

## Examples

### cURL: Send SMS

```bash
curl -X POST http://your-app.com/api/notifyre/sms \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "body": "Hello from cURL!",
    "recipients": [ { "type": "mobile_number", "value": "+1234567890" } ],
    "sender": "+1987654321"
  }'
```

### cURL: List Local Messages (requires sender on user)

```bash
curl -X GET "http://your-app.com/api/notifyre/sms" -H "Accept: application/json"
```

### cURL: Get Specific Local Message

```bash
curl -X GET http://your-app.com/api/notifyre/sms/1 -H "Accept: application/json"
```

### cURL: List via Notifyre API (proxy)

```bash
curl -X GET http://your-app.com/api/notifyre/sms/notifyre -H "Accept: application/json"
```

### cURL: Get via Notifyre API (proxy)

```bash
curl -X GET http://your-app.com/api/notifyre/sms/notifyre/abc123 -H "Accept: application/json"
```

## Error Handling

### Common Error Responses

- 422 when sender is missing for local list endpoint
- 404 when local message or recipient is not found
- 500 when upstream or send operation fails

## Response Codes

| Code | Description                        |
|------|------------------------------------|
| 200  | Success                            |
| 201  | Created (SMS accepted for sending) |
| 404  | Not Found                          |
| 422  | Validation/Precondition Failed     |
| 429  | Too Many Requests (Rate Limit)     |
| 500  | Server Error                       |
