# Notifyre Laravel Package - Artisan Commands

This guide covers all the Artisan commands available in the Notifyre package for setup, configuration, and SMS sending.

## Available Commands

### 1. SMS Send Command

Send SMS messages directly from the command line.

```bash
php artisan sms:send {sender?} {recipient?} {message?}
```

#### Arguments

- **`sender`** (optional): The number the SMS will be sent from
- **`recipient`** (optional): The number the SMS will be sent to  
- **`message`** (optional): The message that will be sent

#### Usage Examples

```bash
# Send with all arguments provided
php artisan sms:send "TestApp" "+1234567890" "Hello from Notifyre!"

# Use default sender, specify recipient and message
php artisan sms:send "" "+1234567890" "Hello from Notifyre!"

# Use default recipient, specify sender and message
php artisan sms:send "TestApp" "" "Hello from Notifyre!"

# Use both defaults, only specify message
php artisan sms:send "" "" "Hello from Notifyre!"
```

#### Default Values

The command automatically uses default values from your configuration:

- **Default Sender**: `config('notifyre.default_sender')`
- **Default Recipient**: `config('notifyre.default_recipient')`

#### Error Handling

The command includes comprehensive error handling:

- Validates that a message is provided
- Checks if sender/recipient can be determined
- Handles service exceptions gracefully
- Provides clear error messages

### 2. Configuration Publishing Commands

#### Publish All Configuration

Publishes all configuration files and environment variables at once.

```bash
php artisan notifyre:publish
```

This command runs both `notifyre:publish-config` and `notifyre:publish-env`.

#### Publish Configuration File

Publishes only the configuration file to `config/notifyre.php`.

```bash
php artisan notifyre:publish-config
```

**What gets published:**
- Configuration file with all default values
- Available options and their descriptions
- Driver-specific settings

#### Publish Environment Variables

Adds all Notifyre environment variables to your `.env` file.

```bash
php artisan notifyre:publish-env
```

**Environment variables added:**
```env
NOTIFYRE_API_TOKEN=your_api_token_here
NOTIFYRE_DRIVER=log
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

## Command Options

### Verbose Output

All commands support Laravel's standard verbose options:

```bash
# Show detailed output
php artisan sms:send "TestApp" "+1234567890" "Hello" -v

# Show very detailed output
php artisan sms:send "TestApp" "+1234567890" "Hello" -vv

# Show debug output
php artisan sms:send "TestApp" "+1234567890" "Hello" -vvv
```

### Quiet Mode

Suppress output for scripting:

```bash
php artisan sms:send "TestApp" "+1234567890" "Hello" -q
```

## Integration with Laravel

### Service Provider Registration

All commands are automatically registered when the package is installed:

```php
// In NotifyreServiceProvider
private const array COMMANDS = [
    NotifyreSmsSendCommand::class,
    PublishNotifyreConfigCommand::class,
    PublishNotifyreEnvCommand::class,
    PublishNotifyreAllCommand::class,
];
```

### Console-Only Registration

Commands are only registered when running in console mode:

```php
if ($this->app->runningInConsole()) {
    $this->commands(self::COMMANDS);
}
```

## Customization

### Extending Commands

You can extend the SMS send command for custom functionality:

```php
<?php

namespace App\Console\Commands;

use Arbi\Notifyre\Commands\NotifyreSmsSendCommand;

class CustomSmsSendCommand extends NotifyreSmsSendCommand
{
    protected $signature = 'custom:sms:send {sender?} {recipient?} {message?}';
    
    protected $description = 'Custom SMS sending command with additional features';
    
    protected function handle(): void
    {
        // Custom logic before sending
        $this->info('Custom SMS sending...');
        
        // Call parent implementation
        parent::handle();
        
        // Custom logic after sending
        $this->info('Custom SMS sent!');
    }
}
```

### Custom Command Registration

Register your custom commands in your service provider:

```php
public function boot(): void
{
    if ($this->app->runningInConsole()) {
        $this->commands([
            CustomSmsSendCommand::class,
        ]);
    }
}
```

## Testing Commands

### Unit Testing

Test commands using Laravel's command testing helpers:

```php
<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use Illuminate\Support\Facades\Config;

class NotifyreSmsSendCommandTest extends TestCase
{
    public function test_sms_send_command_with_all_arguments()
    {
        $this->artisan('sms:send', [
            'sender' => 'TestApp',
            'recipient' => '+1234567890',
            'message' => 'Test message'
        ])->assertSuccessful();
    }
    
    public function test_sms_send_command_uses_defaults()
    {
        Config::set('notifyre.default_sender', 'DefaultApp');
        Config::set('notifyre.default_recipient', '+1234567890');
        
        $this->artisan('sms:send', [
            'message' => 'Test message'
        ])->assertSuccessful();
    }
}
```

### Integration Testing

Test commands with actual service integration:

```php
public function test_sms_send_command_integration()
{
    // Mock the service
    $mockService = Mockery::mock(NotifyreServiceInterface::class);
    $mockService->shouldReceive('send')->once();
    
    $this->app->instance('notifyre', $mockService);
    
    $this->artisan('sms:send', [
        'sender' => 'TestApp',
        'recipient' => '+1234567890',
        'message' => 'Test message'
    ])->assertSuccessful();
}
```

### Available Test Files

The package includes comprehensive tests for all commands:

- **`NotifyreSmsSendCommandTest`** - Tests the main SMS sending command with all scenarios
- **`PublishNotifyreAllCommandTest`** - Tests the command that publishes all configuration files
- **`PublishNotifyreConfigCommandTest`** - Tests the configuration publishing command
- **`PublishNotifyreEnvCommandTest`** - Tests the environment variable publishing command

### Test Coverage

All commands are tested for:

- ✅ **Correct signatures** and descriptions
- ✅ **Command registration** in the service provider
- ✅ **Basic functionality** and error handling
- ✅ **Integration** with Laravel's command system
- ✅ **Property validation** using reflection for protected properties
- ✅ **Environment variable** definitions and default values

## Best Practices

1. **Use the log driver** for testing commands in development
2. **Validate phone numbers** before sending SMS
3. **Handle errors gracefully** in custom command extensions
4. **Use meaningful sender names** for better user experience
5. **Test commands thoroughly** before deploying to production
6. **Use environment-specific configurations** for different deployment stages

## Troubleshooting

### Common Issues

1. **Command not found**: Ensure the package is properly installed and service provider is registered
2. **Configuration errors**: Run `php artisan notifyre:publish-config` to publish configuration
3. **Environment variables missing**: Run `php artisan notifyre:publish-env` to add them
4. **Permission errors**: Ensure your application has write access to `.env` and `config/` directories

### Debug Mode

Enable debug mode to see detailed command execution:

```bash
# Set debug mode
APP_DEBUG=true

# Run command with verbose output
php artisan sms:send "TestApp" "+1234567890" "Hello" -vvv
```

## Next Steps

1. [Learn about usage patterns](./USAGE.md)
2. [See configuration options](./CONFIGURATION.md)
3. [Explore examples](./EXAMPLES.md)
4. [Understand drivers](./DRIVERS.md)
