# Notifyre Laravel Package Testing

This document outlines the testing approach and structure for the Notifyre Laravel package using Pest PHP testing
framework with Orchestra Testbench.

## Testing Philosophy

The testing strategy follows these principles:

- **Comprehensive Coverage**: Every class and method should have corresponding tests
- **Isolation**: Tests should be independent and not affect each other
- **Realistic Scenarios**: Tests should cover both happy path and edge cases
- **Laravel Package Standards**: Use Orchestra Testbench for proper Laravel package testing
- **Fast Execution**: Tests should run quickly with minimal external dependencies

## Test Structure

The package includes comprehensive test scaffolding with **41 test files** covering all components:

### Unit Tests (`tests/Unit/`) - 25 files

Unit tests focus on testing individual classes and methods in isolation:

- **Channels/**: Test the NotifyreChannel for Laravel notifications
- **Contracts/**: Test the NotifyreManager contract
- **DTO/SMS/**: Test data transfer objects for SMS requests and responses (7 files)
- **Enums/**: Test enum classes (NotifyreDriver, NotifyreRecipientTypes, NotifyProcessedStatus)
- **Facades/**: Test the Notifyre facade
- **Models/**: Test Eloquent models and relationships (3 files)
- **Services/**: Test core services and drivers (4 files)
- **Utils/**: Test utility classes and helper functions (4 files)
- **Helpers**: Test global helper functions

### Feature Tests (`tests/Feature/`) - 16 files

Feature tests focus on testing complete workflows and integrations:

- **Commands/**: Test Artisan commands for SMS operations (5 files)
- **Http/Controllers/**: Test API endpoints and request handling (1 file)
- **Http/Requests/**: Test form request validation (2 files)
- **Http/Middlewares/**: Test custom middleware functionality (1 file)
- **Providers/**: Test service provider registration and configuration (7 files)
- **Channels/**: Test notification channel integration (1 file)

## Test Scaffolding

All test files include comprehensive scaffolding with:

- **Descriptive Test Names**: Clear, readable test descriptions
- **TODO Placeholders**: `// TODO: Add test implementation` for easy identification
- **Proper Structure**: Follows Pest PHP conventions
- **Database Traits**: `uses(RefreshDatabase::class)` for database tests
- **Logical Grouping**: Tests organized by functionality (instantiation, validation, error handling, etc.)

### Test Pattern Example

```php
<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be instantiated', function () {
    // TODO: Add test implementation
});

it('can store data', function () {
    // TODO: Add test implementation
});

it('can be serialized', function () {
    // TODO: Add test implementation
});

it('can be converted to array', function () {
    // TODO: Add test implementation
});
```

## Test Configuration

### TestCase Setup

The `tests/TestCase.php` extends Orchestra Testbench and provides:

- **Package Provider Registration**: Automatically registers the NotifyreServiceProvider
- **Database Configuration**: Sets up in-memory SQLite database for testing
- **Configuration Loading**: Loads the notifyre configuration file
- **Laravel Package Environment**: Proper Laravel package testing environment

### Environment Variables

Tests use the following environment variables (defined in `phpunit.xml`):

- `NOTIFYRE_DRIVER`: Test driver (set to 'sms')
- `NOTIFYRE_API_KEY`: Test API key (set to 'test-api-key')
- `NOTIFYRE_USE_UUID`: Use UUIDs for junction table (set to 'true')
- `NOTIFYRE_LOG_LEVEL`: Log level for testing (set to 'debug')

**Note:** All other configuration options use their default values from `config/notifyre.php`. This simplified approach reduces test configuration overhead and ensures tests run with production-like settings.

## Running Tests

### Prerequisites

Install dependencies:

```bash
composer install
```

### Run All Tests

```bash
composer test
# or
vendor/bin/pest
```

### Run Specific Test Suites

```bash
# Run only unit tests
vendor/bin/pest tests/Unit

# Run only feature tests  
vendor/bin/pest tests/Feature
```

### Run Tests with Coverage

```bash
# Generate coverage report
vendor/bin/pest --coverage
```

### Run Tests in Parallel

```bash
vendor/bin/pest --parallel
```

## Test Helpers

The `tests/Pest.php` file provides global helper functions:

- `notifyreConfig($key, $default)`: Access notifyre configuration
- `createNotifyreTestUser($attributes)`: Create test recipients
- `createNotifyreTestMessage($attributes)`: Create test SMS messages

## Testing Approach

### Unit Testing Strategy

**Models**: Test Eloquent models with database interactions

- Factory creation and relationships
- Fillable attributes and casts
- Model methods and scopes

**Services**: Test business logic in isolation

- Mock external dependencies
- Test error handling and edge cases
- Verify configuration usage

**DTOs**: Test data transfer objects

- Constructor validation
- Array conversion methods
- Serialization/deserialization

**Enums**: Test enum values and methods

- Value validation
- String conversion
- Comparison operations
- Status success detection (for NotifyProcessedStatus)
- Null-safe operations

### Feature Testing Strategy

**Commands**: Test Artisan commands end-to-end

- Argument and option handling
- Service integration
- Output formatting

**Controllers**: Test HTTP endpoints

- Request validation
- Response formatting
- Error handling

**Providers**: Test service provider registration

- Service binding
- Configuration publishing
- Command registration

## Test Examples

### Unit Test Example

```php
// tests/Unit/Services/NotifyreServiceTest.php
it('can send SMS', function () {
    $service = new NotifyreService();
    $recipient = new Recipient('mobile_number', '+1234567890');
    $message = new RequestBody('Test message', [$recipient]);
    
    $result = $service->send($message);
    
    expect($result)->toBeInstanceOf(ResponseBody::class);
});
```

### Feature Test Example

```php
// tests/Feature/Commands/NotifyreSmsSendCommandTest.php
it('can send SMS via command', function () {
    $this->artisan('notifyre:sms:send', [
        'message' => 'Test message',
        'recipients' => '+1234567890'
    ])
    ->assertExitCode(0);
});
```

### Model Test Example

```php
// tests/Unit/Models/NotifyreRecipientsTest.php
it('can be created with factory', function () {
    $recipient = NotifyreRecipients::factory()->create([
        'phone_number' => '+1234567890'
    ]);
    
    expect($recipient->phone_number)->toBe('+1234567890');
});
```

### Enum Test Example

```php
// tests/Unit/Enums/NotifyProcessedStatusTest.php
it('identifies successful statuses correctly', function () {
    expect(NotifyProcessedStatus::SENT->isSuccessful())->toBeTrue()
        ->and(NotifyProcessedStatus::DELIVERED->isSuccessful())->toBeTrue()
        ->and(NotifyProcessedStatus::FAILED->isSuccessful())->toBeFalse()
        ->and(NotifyProcessedStatus::UNDELIVERABLE->isSuccessful())->toBeFalse();
});

it('checks if status string is successful', function () {
    expect(NotifyProcessedStatus::isStatusSuccessful('sent'))->toBeTrue()
        ->and(NotifyProcessedStatus::isStatusSuccessful('delivered'))->toBeTrue()
        ->and(NotifyProcessedStatus::isStatusSuccessful('failed'))->toBeFalse()
        ->and(NotifyProcessedStatus::isStatusSuccessful('undeliverable'))->toBeFalse()
        ->and(NotifyProcessedStatus::isStatusSuccessful(null))->toBeFalse();
});
```

## Best Practices

1. **Isolation**: Each test should be independent and not affect others
2. **Mocking**: Mock external dependencies to avoid side effects
3. **Database**: Use RefreshDatabase trait for tests that interact with the database
4. **Naming**: Use descriptive test names that explain what is being tested
5. **Structure**: Follow the existing directory structure and naming conventions
6. **Configuration**: Use test-specific configuration and environment variables
7. **Assertions**: Use appropriate Pest assertions for clear test failures

## Database Testing

Tests that interact with the database should:

- Use the `RefreshDatabase` trait
- Create test data using factories
- Test both creation and retrieval of data
- Verify relationships between models

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a recipient', function () {
    $recipient = NotifyreRecipients::factory()->create();
    
    expect($recipient)->toBeInstanceOf(NotifyreRecipients::class);
    expect($recipient->exists)->toBeTrue();
});
```

## Mocking External Services

For tests that interact with external APIs:

- Mock HTTP clients to avoid actual API calls
- Test both success and error scenarios
- Verify that correct data is sent to external services

## Troubleshooting

### Common Issues

1. **Orchestra Testbench not found**: Ensure it's installed as a dev dependency
2. **Database connection errors**: Check that testbench database is configured
3. **Configuration errors**: Verify that notifyre config is loaded in TestCase
4. **Factory errors**: Ensure model factories are properly registered

### Debug Mode

Run tests with verbose output:

```bash
vendor/bin/pest --verbose
```

## Testing GitHub Actions Workflow Locally

You can test the GitHub Actions workflow locally using tools like `act` to ensure your CI/CD pipeline works correctly
before pushing changes.

### Prerequisites

Install `act` (GitHub Actions local runner):

```bash
# macOS
brew install act

# Linux (using curl)
curl https://raw.githubusercontent.com/nektos/act/master/install.sh | sudo bash

# Windows (using Chocolatey)
choco install act-cli

# Or download from GitHub releases
# https://github.com/nektos/act/releases
```

### Running the Workflow Locally

#### Basic Test Run

```bash
# Run the entire workflow
act

# Run only specific job
act -j validation
```

#### With Environment Variables

```bash
# Run with environment variables
act --env NOTIFYRE_API_KEY=test-key --env NOTIFYRE_API_URL=https://api.test.com
```

#### Dry Run (List Actions)

```bash
# See what would run without executing
act --list
```

#### Using Specific Event

```bash
# Simulate a push event
act push

# Simulate a pull request event
act pull_request
```

### Workflow Configuration

The workflow is configured to run on:

- **Push to main branch**
- **Pull requests to main branch**

### Local Testing Benefits

- **Fast Feedback**: Test CI changes without pushing to GitHub
- **Debug Issues**: Easily debug workflow problems locally
- **Validate Changes**: Ensure workflow modifications work correctly
- **Save CI Minutes**: Avoid using GitHub Actions minutes for testing

### Common Act Commands

```bash
# Run with verbose output
act -v

# Run with specific PHP version
act --env PHP_VERSION=8.4

# Run with custom working directory
act --workdir /path/to/your/project

# Run and keep containers for debugging
act --rm=false
```

### Troubleshooting Act

#### Docker Issues

```bash
# Ensure Docker is running
docker --version

# Check if act can access Docker
act --list
```

#### Permission Issues

```bash
# Run with sudo if needed (Linux)
sudo act

# Or add user to docker group
sudo usermod -aG docker $USER
```

#### Workflow Not Found

```bash
# Specify workflow file explicitly
act -W .github/workflows/tests.yml
```

### Workflow Structure

The current workflow includes a single **validation** job that runs:

1. **Code Checkout**: Checkout the repository code
2. **PHP Setup**: Configure PHP 8.4 with required extensions
3. **Dependencies**: Install dependencies using `composer fresh`
4. **Code Style**: Run Pint code style checks using `composer format-test`
5. **Tests**: Execute tests using `composer test`

### Customizing for Local Testing

You can create a local-specific workflow file for testing:

```yaml
# .github/workflows/tests-local.yml
name: Tests (Local)

on:
  workflow_dispatch:  # Manual trigger only

jobs:
  validation:
    runs-on: ubuntu-latest
    # ... same as main workflow
```

Then run with:

```bash
act -W .github/workflows/tests-local.yml
```

## Contributing

When adding new tests:

1. Follow the existing test structure and naming conventions
2. Use descriptive test names that explain the scenario
3. Add appropriate mocking for external dependencies
4. Place tests in the correct directory (Unit vs Feature)
5. Test the workflow locally with `act` before pushing
6. Update this documentation if adding new test patterns
