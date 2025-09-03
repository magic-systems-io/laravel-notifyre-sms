# Notifyre Laravel Package Tests

This directory contains comprehensive tests for the Notifyre Laravel package using Pest PHP testing framework.

## Test Structure

### Unit Tests (`tests/Unit/`)

- **DTO Tests**: Test data transfer objects for SMS requests and responses
- **Enum Tests**: Test the NotifyreRecipientTypes enum
- **Service Tests**: Test core services like NotifyreService and DriverFactory
- **Driver Tests**: Test SMS and Log drivers with response handling
- **Channel Tests**: Test the NotifyreChannel for Laravel notifications
- **Exception Tests**: Test custom exceptions
- **Facade Tests**: Test the Notifyre facade
- **Provider Tests**: Test the service provider
- **Helper Tests**: Test helper functions

### Feature Tests (`tests/Feature/`)

- **Command Tests**: Test Artisan commands with response handling
- **Integration Tests**: Test complete package workflows including response data

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
- **Response DTOs**: Mock ResponseBodyDTO for testing response handling

## Test Helpers

The `tests/Pest.php` file provides global helper functions:

- `notifyreTestConfig()`: Create test configuration
- `createTestMessage()`: Create test RequestBodyDTO
- `createTestRecipient()`: Create test Recipient
- `createMockResponse()`: Create mock ResponseBodyDTO for testing

## Test Coverage

The test suite covers:

- ✅ **100% DTO Coverage**: All data transfer objects including Arrayable interface
- ✅ **100% Enum Coverage**: All enum values and methods
- ✅ **100% Service Coverage**: All service classes with response handling
- ✅ **100% Driver Coverage**: SMS and Log drivers with response returns
- ✅ **100% Channel Coverage**: Notification channel
- ✅ **100% Exception Coverage**: Custom exceptions
- ✅ **100% Facade Coverage**: Facade functionality
- ✅ **100% Provider Coverage**: Service provider
- ✅ **100% Helper Coverage**: Helper functions
- ✅ **100% Command Coverage**: Artisan commands with response handling
- ✅ **Integration Coverage**: Complete workflows including response data

## Test Scenarios

### DTO Tests

- Valid data creation with all new parameters
- Validation errors for metadata limits
- Edge cases (empty values, special characters)
- Multiple recipients with different types
- **Arrayable interface testing** (`toArray()` method)
- **Metadata validation** (key/value length limits)
- **Response DTO testing** with payload and error handling

### Service Tests

- Driver factory creation
- Service delegation with response handling
- Configuration handling
- Error scenarios
- **Response data validation**

### Driver Tests

- SMS driver HTTP requests with response parsing
- Log driver logging with mock response generation
- Configuration validation
- Error handling
- **Response DTO creation and validation**

### Channel Tests

- Notification sending with response handling
- Method validation
- Error handling
- Multiple recipients
- **Response data flow through notifications**

### Command Tests

- Argument handling
- Default value usage
- Error handling
- Service integration
- **Response output formatting**

## Response Testing

### Testing Response DTOs

```php
test('service returns response data', function () {
    $response = notifyre()->send($testMessage);
    
    expect($response)->toBeInstanceOf(ResponseBodyDTO::class);
    expect($response->success)->toBeTrue();
    expect($response->payload)->toBeInstanceOf(ResponsePayload::class);
    expect($response->payload->smsMessageID)->not->toBeEmpty();
});
```

### Testing Arrayable Interface

```php
test('dto implements arrayable interface', function () {
    $dto = new RequestBodyDTO(
        body: 'Test message',
        recipients: [new Recipient('mobile_number', '+1234567890')],
        metadata: ['key' => 'value']
    );
    
    $array = $dto->toArray();
    
    expect($array)->toHaveKey('Body');
    expect($array)->toHaveKey('Recipients');
    expect($array)->toHaveKey('Metadata');
});
```

### Testing Metadata Validation

```php
test('metadata validation limits', function () {
    $metadata = [];
    for ($i = 0; $i < 51; $i++) {
        $metadata["key_{$i}"] = "value_{$i}";
    }
    
    expect(fn() => new RequestBodyDTO(
        body: 'Test',
        recipients: [new Recipient('mobile_number', '+1234567890')],
        metadata: $metadata
    ))->toThrow(InvalidArgumentException::class, 'Metadata cannot exceed 50 keys');
});
```

## Best Practices

1. **Isolation**: Each test is independent and doesn't affect others
2. **Mocking**: External dependencies are mocked to avoid side effects
3. **Configuration**: Tests use isolated configuration
4. **Coverage**: Aim for 100% code coverage
5. **Readability**: Tests are descriptive and easy to understand
6. **Maintainability**: Tests are organized and well-structured
7. **Response Testing**: Always test response data structure and content
8. **Arrayable Testing**: Test `toArray()` methods for all DTOs

## Troubleshooting

### Common Issues

1. **Mockery not found**: Ensure Mockery is installed and imported
2. **Configuration errors**: Check that test configuration is set up correctly
3. **HTTP client errors**: Ensure HTTP facade is properly mocked
4. **Logging errors**: Ensure Log facade is properly mocked
5. **Response DTO errors**: Ensure ResponseBodyDTO is properly imported and mocked

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
7. **Test response handling** for new features
8. **Test Arrayable interface** for new DTOs

## CI/CD Integration

The test suite is designed to work with CI/CD pipelines:

- **Parallel execution**: Use `--parallel` flag
- **Sharding**: Use `--shard` flag for large test suites
- **Coverage reporting**: Generate coverage reports for quality gates
- **Exit codes**: Tests exit with proper codes for CI/CD integration
- **Response validation**: Automated testing of response data structure
