# CLI Commands

Send SMS messages directly from the command line using Artisan commands.

## Quick Start

### Basic Usage

```bash
# Send SMS to a single recipient
php artisan sms:send --message="Hello from CLI!" --recipient="+1234567890"

# Send SMS to multiple recipients
php artisan sms:send --message="Meeting reminder" --recipient="+1234567890" --recipient="+9876543210"

# Send SMS with specific recipient type
php artisan sms:send --message="Welcome!" --recipient="+1234567890:mobile_number" --recipient="contact_123:contact"
```

## Command Reference

### `sms:send`

Sends SMS messages via the command line.

#### Signature

```bash
php artisan sms:send {--r|recipient=* : The number and optional type, e.g. +123456789:mobile_number,+987654321:contact} {--m|message= : The message that will be sent}
```

#### Options

| Option        | Short | Required | Description                                  |
|---------------|-------|----------|----------------------------------------------|
| `--recipient` | `-r`  | ✅        | Recipient phone number(s) with optional type |
| `--message`   | `-m`  | ✅        | SMS message content                          |

#### Runtime Output

- On start: `Sending SMS...`
- On success: `SMS is being sent`
- On failure: `Failed to send SMS: <reason>` followed by `Use --help to see usage information.`

Exit codes follow Symfony's conventions (0 for success, non-zero for failure).

#### Recipient Format

Recipients can be specified in two formats:

1. **Phone number only** (defaults to `mobile_number` type):
   ```
   +1234567890
   ```

2. **Phone number with type**:
   ```
   +1234567890:mobile_number
   +1234567890:contact
   +1234567890:group
   ```

### `sms:list`

Lists SMS messages with filtering options via the Notifyre API. See API docs for available filters.

> Note: The exact signature and filtering options are subject to change; consult `php artisan sms:list --help` in your
> project for the up-to-date options.

### Publishing Commands

#### `notifyre:publish`

Publishes all Notifyre configuration files.

```bash
php artisan notifyre:publish
```

#### `notifyre:publish-config`

Publishes the configuration file.

```bash
php artisan notifyre:publish-config
```

#### `notifyre:publish-env`

Adds environment variables to your .env file.

```bash
php artisan notifyre:publish-env
```

## Examples

### Basic SMS

```bash
php artisan sms:send --message="Hello World!" --recipient="+1234567890"
```

### Multiple Recipients

```bash
php artisan sms:send \
  --message="Meeting reminder: 2 PM today" \
  --recipient="+1234567890" \
  --recipient="+9876543210" \
  --recipient="+5551234567"
```

### Different Recipient Types

```bash
# Send to mobile number and contact
php artisan sms:send \
  --message="Welcome to our service!" \
  --recipient="+1234567890:mobile_number" \
  --recipient="contact_123:contact"

# Send to group
php artisan sms:send \
  --message="Group announcement: New features available!" \
  --recipient="group_456:group"
```

### Long Messages

```bash
php artisan sms:send \
  --message="This is a longer message that demonstrates how to send SMS with more content. It can be up to 160 characters long." \
  --recipient="+1234567890"
```

### List SMS Messages

```bash
# Help output for filters
php artisan sms:list --help | cat

# List recent SMS messages (example; options depend on your version)
php artisan sms:list
```

## Configuration

### Environment Variables

Make sure you have the required configuration:

```env
# Required
NOTIFYRE_DRIVER=sms
NOTIFYRE_API_KEY=your_api_key_here

# Optional
# NOTIFYRE_LOG_LEVEL=debug  # emergency|alert|critical|error|warning|notice|info|debug
```

**Note:** Most settings are configured in `config/notifyre.php`. Publish the config to customize.

### Testing Mode

For testing, use the log driver:

```env
NOTIFYRE_DRIVER=log
```

This will log the SMS to Laravel logs instead of sending via API.

## Error Handling

### Common Errors

#### Missing Message

```bash
$ php artisan sms:send --recipient="+1234567890"
Exception: You must provide a message to send.
```

#### Missing Recipients

```bash
$ php artisan sms:send --message="Hello"
Exception: You must provide a recipient to send the SMS to.
```

#### Invalid Configuration

```bash
$ php artisan sms:send --message="Hello" --recipient="+1234567890"
Exception: Invalid Notifyre driver ''. Supported drivers are: sms, log
```

### Success Output

```bash
$ php artisan sms:send --message="Hello" --recipient="+1234567890"
Sending SMS...
SMS is being sent
```

## Debugging

### Verbose Output

For more detailed output, you can use Laravel's verbose flag:

```bash
php artisan sms:send --message="Hello" --recipient="+1234567890" -v
```

### Check Configuration

Verify your configuration is correct:

```bash
php artisan config:show notifyre | cat
```

### Test with Log Driver

Test the command without sending real SMS:

```bash
# Set log driver
export NOTIFYRE_DRIVER=log

# Run command
php artisan sms:send --message="Test message" --recipient="+1234567890"

# Check logs
tail -f storage/logs/notifyre_sms.log
# Or
tail -f storage/logs/laravel.log
```

## Integration

### In Scripts

You can integrate the command into shell scripts:

```bash
#!/bin/bash

# Send notification SMS
php artisan sms:send \
  --message="Server backup completed successfully" \
  --recipient="+1234567890"

# Check exit code
if [ $? -eq 0 ]; then
    echo "SMS notification sent"
else
    echo "Failed to send SMS notification"
fi
```

### In Cron Jobs

Schedule regular SMS notifications:

```bash
# Add to crontab
0 9 * * 1 php /path/to/your/app/artisan sms:send --message="Weekly reminder: Check your tasks" --recipient="+1234567890"
```

### In Deployment Scripts

Send deployment notifications:

```bash
#!/bin/bash

# Deploy application
# ... deployment steps ...

# Send success notification
php artisan sms:send \
  --message="Deployment completed successfully at $(date)" \
  --recipient="+1234567890"
```

## Next Steps

- Learn about [Direct SMS usage](./DIRECT_SMS.md)
- Explore [Laravel notifications](./NOTIFICATIONS.md)
- Check out [REST API usage](./API.md)
- Review [Configuration options](../getting-started/CONFIGURATION.md)
