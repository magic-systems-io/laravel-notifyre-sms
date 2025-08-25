# REST API Usage

The Notifyre Laravel package provides a comprehensive REST API for SMS operations, including sending messages, retrieving message history, and managing recipients.

## 🚀 Quick Start

### Enable API

Make sure the API is enabled in your configuration:

```env
NOTIFYRE_API_ENABLED=true
NOTIFYRE_API_PREFIX=notifyre
```

### Routes

The package automatically registers these API routes:

- `POST /api/notifyre/sms` - Send SMS messages
- `GET /api/notifyre/sms` - List SMS messages
- `GET /api/notifyre/sms/{id}` - Get specific SMS message

## 📤 Sending SMS

### Endpoint

```
POST /api/notifyre/sms
```

### Request Body

```json
{
    "body": "Hello from the API!",
    "recipients": [
        {
            "type": "virtual_mobile_number",
            "value": "+1234567890"
        }
    ],
    "sender": "+1987654321",
    "persist": true
}
```

### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `body` | string | ✅ | SMS message content (max 160 characters) |
| `recipients` | array | ✅ | Array of recipient objects |
| `recipients[].type` | string | ✅ | Recipient type: `virtual_mobile_number`, `contact`, or `group` |
| `recipients[].value` | string | ✅ | Recipient value (phone number, contact ID, or group ID) |
| `sender` | string | ❌ | Sender phone number (uses default if not provided) |
| `persist` | boolean | ❌ | Whether to store the message in database (defaults to config) |

### Response

```json
{
    "data": {
        "body": "Hello from the API!",
        "recipients": [
            {
                "type": "virtual_mobile_number",
                "value": "+1234567890"
            }
        ],
        "sender": "+1987654321"
    },
    "failed_recipients": []
}
```

### Error Response

```json
{
    "message": "Failed to send SMS"
}
```

## 📥 Retrieving Messages

### List All Messages

```
GET /api/notifyre/sms
```

#### Query Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `page` | integer | Page number for pagination |
| `per_page` | integer | Items per page |

#### Response

```json
{
    "data": [
        {
            "id": 1,
            "messageId": "sms-123",
            "sender": "+1987654321",
            "body": "Hello from the API!",
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z",
            "messageRecipients": [
                {
                    "id": 1,
                    "type": "virtual_mobile_number",
                    "value": "+1234567890",
                    "created_at": "2024-01-15T10:30:00.000000Z",
                    "updated_at": "2024-01-15T10:30:00.000000Z"
                }
            ]
        }
    ],
    "current_page": 1,
    "per_page": 15,
    "total": 1
}
```

### Get Specific Message

```
GET /api/notifyre/sms/{id}
```

#### Response

```json
{
    "id": 1,
    "messageId": "sms-123",
    "sender": "+1987654321",
    "body": "Hello from the API!",
    "created_at": "2024-01-15T10:30:00.000000Z",
    "updated_at": "2024-01-15T10:30:00.000000Z",
    "messageRecipients": [
        {
            "id": 1,
            "type": "virtual_mobile_number",
            "value": "+1234567890",
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        }
    ]
}
```

#### Error Response

```json
{
    "error": "Message not found"
}
```

## 🔧 Configuration

### API Settings

```php
// config/notifyre.php
'api' => [
    'enabled' => env('NOTIFYRE_API_ENABLED', true),
    'prefix' => env('NOTIFYRE_API_PREFIX', 'notifyre'),
    'middleware' => explode(',', env('NOTIFYRE_API_MIDDLEWARE', 'api')),
    'rate_limit' => [
        'enabled' => env('NOTIFYRE_RATE_LIMIT_ENABLED', true),
        'max_requests' => env('NOTIFYRE_RATE_LIMIT_MAX_REQUESTS', 60),
        'decay_minutes' => env('NOTIFYRE_RATE_LIMIT_DECAY_MINUTES', 1),
    ],
    'database' => [
        'enabled' => env('NOTIFYRE_DB_ENABLED', true),
    ],
    'cache' => [
        'enabled' => env('NOTIFYRE_CACHE_ENABLED', true),
        'ttl' => env('NOTIFYRE_CACHE_TTL', 3600),
        'prefix' => env('NOTIFYRE_CACHE_PREFIX', 'notifyre_'),
    ],
],
```

### Environment Variables

```env
# API Configuration
NOTIFYRE_API_ENABLED=true
NOTIFYRE_API_PREFIX=notifyre
NOTIFYRE_API_MIDDLEWARE=api

# Rate Limiting
NOTIFYRE_RATE_LIMIT_ENABLED=true
NOTIFYRE_RATE_LIMIT_MAX_REQUESTS=60
NOTIFYRE_RATE_LIMIT_DECAY_MINUTES=1

# Database
NOTIFYRE_DB_ENABLED=true

# Caching
NOTIFYRE_CACHE_ENABLED=true
NOTIFYRE_CACHE_TTL=3600
NOTIFYRE_CACHE_PREFIX=notifyre_
```

## 🛡️ Security & Middleware

### Default Middleware

The API uses Laravel's `api` middleware by default, which includes:

- Authentication (if configured)
- Rate limiting
- CORS handling
- JSON validation

### Custom Middleware

You can customize the middleware stack:

```env
NOTIFYRE_API_MIDDLEWARE=auth:sanctum,throttle:60,1
```

## 📊 Rate Limiting

### Default Limits

- **60 requests per minute** per user/IP
- **Configurable** via environment variables

### Custom Limits

```env
NOTIFYRE_RATE_LIMIT_MAX_REQUESTS=100
NOTIFYRE_RATE_LIMIT_DECAY_MINUTES=5
```

## 💾 Database Persistence

### Automatic Storage

When `persist` is `true` or `NOTIFYRE_DB_ENABLED=true`, messages are automatically stored in the database.

### Database Schema

The package creates these tables:

- `notifyre_sms_messages` - Stores SMS message details
- `notifyre_recipients` - Stores recipient information
- `notifyre_sms_message_recipients` - Junction table for many-to-many relationship

## 🚀 Caching

### Response Caching

API responses are cached when enabled:

```env
NOTIFYRE_CACHE_ENABLED=true
NOTIFYRE_CACHE_TTL=3600
NOTIFYRE_CACHE_PREFIX=notifyre_
```

### Cache Keys

- `{prefix}.{sms_message_id}` - Caches individual SMS responses

## 🔍 Error Handling

### Validation Errors

The API validates all input and returns detailed error messages for invalid data.

### SMS Sending Errors

If SMS sending fails, the API returns:

- HTTP 422 status for validation failures
- HTTP 500 status for server errors
- Detailed error messages in the response

### Common Error Scenarios

- Invalid recipient phone numbers
- Empty message body
- Invalid recipient types
- API authentication failures
- Rate limit exceeded

## 📱 Example Usage

### cURL Example

```bash
curl -X POST http://your-app.com/api/notifyre/sms \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "body": "Hello from cURL!",
    "recipients": [
      {
        "type": "virtual_mobile_number",
        "value": "+1234567890"
      }
    ],
    "sender": "+1987654321"
  }'
```

### JavaScript Example

```javascript
const response = await fetch('/api/notifyre/sms', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    body: JSON.stringify({
        body: 'Hello from JavaScript!',
        recipients: [
            {
                type: 'virtual_mobile_number',
                value: '+1234567890'
            }
        ],
        sender: '+1987654321'
    })
});

const result = await response.json();
console.log(result);
```

### PHP Example

```php
use Illuminate\Support\Facades\Http;

$response = Http::post('/api/notifyre/sms', [
    'body' => 'Hello from PHP!',
    'recipients' => [
        [
            'type' => 'virtual_mobile_number',
            'value' => '+1234567890'
        ]
    ],
    'sender' => '+1987654321'
]);

$result = $response->json();
```

## 🔗 Next Steps

- Learn about [Direct SMS usage](./DIRECT_SMS.md)
- Explore [Laravel notifications](./NOTIFICATIONS.md)
- Check out [CLI commands](./COMMANDS.md)
- Review [Configuration options](../getting-started/CONFIGURATION.md)
