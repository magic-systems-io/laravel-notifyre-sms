# Commands

Send SMS messages from the command line using Artisan commands.

## SMS Send Command

Send SMS messages directly from the terminal:

```bash
php artisan sms:send --from="+1234567890" --recipient="+0987654321" --message="Hello World!"
```

### Options

- **`--from`** - The number the SMS will be sent from (optional)
- **`--recipient`** - The number the SMS will be sent to  
- **`--message`** - The message that will be sent

### Examples

```bash
# Send a test message
php artisan sms:send --from="+1234567890" --recipient="+0987654321" --message="Test message"

# Send with default sender/recipient from config
php artisan sms:send --message="Meeting reminder at 2 PM"

# Send a longer message
php artisan sms:send --message="Your order #12345 has been shipped and will arrive tomorrow"
```

### Configuration

If you don't specify `--from` or `--recipient`, the command will use your config defaults:

```env
NOTIFYRE_SMS_FROM=+1234567890
NOTIFYRE_SMS_RECIPIENT=+0987654321
```

### What Happens

1. Command validates your input
2. Creates a `RequestBodyDTO` with your message using the new structure
3. Uses `NotifyreRecipientTypes::VIRTUAL_MOBILE_NUMBER` for recipient type
4. Sends it through the NotifyreService
5. Shows success/error message with response details

### Error Handling

The command will show helpful error messages for:
- Missing message text
- Invalid phone numbers
- Configuration issues
- API errors
- Validation errors from the new DTO structure

### Testing

Perfect for testing your SMS setup:

```bash
# Test with log driver (no real SMS sent)
NOTIFYRE_DRIVER=log php artisan sms:send --message="Test message"

# Test with real SMS driver
NOTIFYRE_DRIVER=sms php artisan sms:send --message="Production test"
```

### Response Information

The command now provides detailed response information:

```bash
php artisan sms:send --message="Test message"
# Output will include:
# - Success status
# - Message ID
# - Friendly ID
# - Any invalid numbers with reasons
```
