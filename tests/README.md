# Notifyre Laravel Package Tests

This directory contains comprehensive tests for the Notifyre Laravel package using Pest PHP testing framework.

## Test Structure

### Unit Tests (`tests/Unit/`)

- **DTO Tests**: Test data transfer objects for SMS requests and responses
- **Enum Tests**: Test the NotifyreDriver enum
- **Service Tests**: Test core services like NotifyreService and DriverFactory
- **Driver Tests**: Test SMS and Log drivers
- **Channel Tests**: Test the NotifyreChannel for Laravel notifications
- **Exception Tests**: Test custom exceptions
- **Facade Tests**: Test the Notifyre facade
- **Provider Tests**: Test the service provider
- **Helper Tests**: Test helper functions

### Feature Tests (`tests/Feature/`)

- **Command Tests**: Test Artisan commands
- **Integration Tests**: Test complete package workflows

## Running Tests

### Prerequisites

Make sure you have the required dependencies installed:

```bash
composer install
```

### Run All Tests

```bash
./vendor/bin/pest
```

### Run Specific Test Suites

```bash
# Run only unit tests
./vendor/bin/pest --testsuite=Unit

# Run only feature tests
./vendor/bin/pest --testsuite=Feature
```

### Run Tests with Coverage

```bash
# Generate HTML coverage report
./vendor/bin/pest --coverage-html=coverage

# Generate text coverage report
./vendor/bin/pest --coverage-text
```

### Run Tests in Parallel

```bash
./vendor/bin/pest --parallel
```

### Run Tests with Sharding (for CI/CD)

```bash
# Run first shard
./vendor/bin/pest --shard=1/4

# Run second shard
./vendor/bin/pest --shard=2/4
```

## Test Configuration

The tests use a test configuration that:

- Sets the driver to `log` by default (to avoid actual SMS sending)
- Uses test API keys and URLs
- Disables caching
- Sets reasonable timeouts and retry settings

## Mocking

Tests use Mockery for mocking dependencies:

- **Services**: Mock NotifyreService to avoid actual SMS sending
- **HTTP Client**: Mock HTTP requests in SMSDriver tests
- **Logging**: Mock Laravel's Log facade in LogDriver tests

## Test Helpers

The `tests/Pest.php` file provides global helper functions:

- `notifyreTestConfig()`: Create test configuration
- `createTestMessage()`: Create test RequestBodyDTO
- `createTestRecipient()`: Create test Recipient

## Test Coverage

The test suite covers:

- ✅ **100% DTO Coverage**: All data transfer objects
- ✅ **100% Enum Coverage**: All enum values and methods
- ✅ **100% Service Coverage**: All service classes
- ✅ **100% Driver Coverage**: SMS and Log drivers
- ✅ **100% Channel Coverage**: Notification channel
- ✅ **100% Exception Coverage**: Custom exceptions
- ✅ **100% Facade Coverage**: Facade functionality
- ✅ **100% Provider Coverage**: Service provider
- ✅ **100% Helper Coverage**: Helper functions
- ✅ **100% Command Coverage**: Artisan commands
- ✅ **Integration Coverage**: Complete workflows

## Test Scenarios

### DTO Tests
- Valid data creation
- Validation errors
- Edge cases (empty values, special characters)
- Multiple recipients

### Service Tests
- Driver factory creation
- Service delegation
- Configuration handling
- Error scenarios

### Driver Tests
- SMS driver HTTP requests
- Log driver logging
- Configuration validation
- Error handling

### Channel Tests
- Notification sending
- Method validation
- Error handling
- Multiple recipients

### Command Tests
- Argument handling
- Default value usage
- Error handling
- Service integration

## Best Practices

1. **Isolation**: Each test is independent and doesn't affect others
2. **Mocking**: External dependencies are mocked to avoid side effects
3. **Configuration**: Tests use isolated configuration
4. **Coverage**: Aim for 100% code coverage
5. **Readability**: Tests are descriptive and easy to understand
6. **Maintainability**: Tests are organized and well-structured

## Troubleshooting

### Common Issues

1. **Mockery not found**: Ensure Mockery is installed and imported
2. **Configuration errors**: Check that test configuration is set up correctly
3. **HTTP client errors**: Ensure HTTP facade is properly mocked
4. **Logging errors**: Ensure Log facade is properly mocked

### Debug Mode

Run tests with verbose output:

```bash
./vendor/bin/pest --verbose
```

### Test Database

Tests use an in-memory SQLite database by default. If you need a different database:

1. Update `phpunit.xml` configuration
2. Ensure database connection is properly configured
3. Run database migrations before tests

## Contributing

When adding new tests:

1. Follow the existing test structure
2. Use descriptive test names
3. Ensure proper mocking
4. Add to appropriate test suite
5. Update this README if needed
6. Maintain 100% coverage for new code

## CI/CD Integration

The test suite is designed to work with CI/CD pipelines:

- **Parallel execution**: Use `--parallel` flag
- **Sharding**: Use `--shard` flag for large test suites
- **Coverage reporting**: Generate coverage reports for quality gates
- **Exit codes**: Tests exit with proper codes for CI/CD integration
