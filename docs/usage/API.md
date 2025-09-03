# REST API Usage

The Notifyre Laravel package provides a comprehensive REST API for SMS operations, including sending messages,
retrieving message history, and managing recipients.

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
- `GET /api/notifyre/sms` - List SMS messages (requires sender parameter)
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
            "type": "mobile_number",
            "value": "+1234567890"
        }
    ],
    "sender": "+1987654321"
}
```

### Parameters

| Parameter            | Type    | Required | Description                                                   |
|----------------------|---------|----------|---------------------------------------------------------------|
| `body`               | string  | ✅        | SMS message content (max 160 characters)                      |
| `recipients`         | array   | ✅        | Array of recipient objects                                    |
| `recipients[].type`  | string  | ✅        | Recipient type: `mobile_number`, `contact`, or `group`        |
| `recipients[].value` | string  | ✅        | Recipient value (phone number, contact ID, or group ID)       |
| `sender`             | string  | ❌        | Sender phone number (uses default if not provided)            |

### Response

```json
{
    "success": true,
    "statusCode": 200,
    "message": "OK",
    "payload": {
        "smsMessageID": "sms-123",
        "friendlyID": "friendly-123",
        "invalidToNumbers": []
    },
    "errors": []
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

**Note**: This endpoint requires a sender parameter to filter messages by sender.

#### Query Parameters

| Parameter  | Type    | Required | Description                |
|------------|---------|----------|----------------------------|
| `sender`   | string  | ✅        | Sender phone number to filter by |

#### Response

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
    "driver": "sms",
    "created_at": "2024-01-15T10:30:00.000000Z",
    "updated_at": "2024-01-15T10:30:00.000000Z",
    "messageRecipients": [
        {
            "id": 1,
            "message_id": 1,
            "recipient_id": 1,
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z",
            "recipient": {
                "id": 1,
                "type": "mobile_number",
                "value": "+1234567890",
                "created_at": "2024-01-15T10:30:00.000000Z",
                "updated_at": "2024-01-15T10:30:00.000000Z"
            }
        }
    ]
}
```

## 🔧 Configuration

### Environment Variables

```env
# API Settings
NOTIFYRE_API_ENABLED=true
NOTIFYRE_API_PREFIX=notifyre
NOTIFYRE_API_MIDDLEWARE=api

# Rate Limiting
NOTIFYRE_RATE_LIMIT_ENABLED=true
NOTIFYRE_RATE_LIMIT_MAX_REQUESTS=60
NOTIFYRE_RATE_LIMIT_DECAY_MINUTES=1

# Database Persistence
NOTIFYRE_DB_ENABLED=true

# Caching
NOTIFYRE_CACHE_ENABLED=true
NOTIFYRE_CACHE_TTL=3600
NOTIFYRE_CACHE_PREFIX=notifyre_
```

### Rate Limiting

The API includes built-in rate limiting:

- **Default**: 60 requests per minute
- **Configurable**: Via environment variables
- **Per IP**: Rate limiting is applied per IP address

### Caching

Response caching is available for improved performance:

- **Message retrieval**: Cached responses for individual messages
- **TTL**: Configurable cache time-to-live
- **Prefix**: Customizable cache key prefix

## 🔐 Authentication

The API uses Laravel's standard authentication middleware. You can configure custom middleware:

```env
NOTIFYRE_API_MIDDLEWARE=api,auth:sanctum
```

## 📝 Examples

### cURL Examples

#### Send SMS

```bash
curl -X POST http://your-app.com/api/notifyre/sms \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "body": "Hello from cURL!",
    "recipients": [
      {
        "type": "mobile_number",
        "value": "+1234567890"
      }
    ],
    "sender": "+1987654321"
  }'
```

#### List Messages

```bash
curl -X GET "http://your-app.com/api/notifyre/sms?sender=%2B1987654321" \
  -H "Accept: application/json"
```

#### Get Specific Message

```bash
curl -X GET http://your-app.com/api/notifyre/sms/1 \
  -H "Accept: application/json"
```

### JavaScript Examples

#### Send SMS

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
        type: 'mobile_number',
        value: '+1234567890'
      }
    ],
    sender: '+1987654321'
  })
});

const result = await response.json();
console.log(result);
```

#### List Messages

```javascript
const response = await fetch('/api/notifyre/sms?sender=%2B1987654321', {
  headers: {
    'Accept': 'application/json',
  }
});

const messages = await response.json();
console.log(messages.data);
```

## 🚨 Error Handling

### Common Error Responses

#### 422 - Validation Error

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "body": ["The body field is required."],
    "recipients": ["The recipients field is required."]
  }
}
```

#### 404 - Message Not Found

```json
{
  "error": "Message not found"
}
```

#### 500 - Server Error

```json
{
  "message": "Failed to send SMS"
}
```

## 🔄 Response Codes

| Code | Description                    |
|------|--------------------------------|
| 200  | Success                        |
| 201  | Created (SMS sent successfully) |
| 400  | Bad Request                    |
| 401  | Unauthorized                   |
| 404  | Not Found                      |
| 422  | Validation Error               |
| 429  | Too Many Requests (Rate Limit) |
| 500  | Server Error                   |
