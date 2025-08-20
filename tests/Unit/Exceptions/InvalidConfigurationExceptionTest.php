<?php

use Arbi\Notifyre\Exceptions\InvalidConfigurationException;

describe('InvalidConfigurationException', function () {
    it('extends Exception class', function () {
        $exception = new InvalidConfigurationException('Test error');

        expect($exception)->toBeInstanceOf(Exception::class);
    });

    it('sets correct message', function () {
        $message = 'Configuration error occurred';
        $exception = new InvalidConfigurationException($message);

        expect($exception->getMessage())->toBe($message);
    });

    it('sets correct code', function () {
        $exception = new InvalidConfigurationException('Test error');

        expect($exception->getCode())->toBe(500);
    });

    it('can be thrown and caught', function () {
        expect(fn() => throw new InvalidConfigurationException('Test error'))
            ->toThrow(InvalidConfigurationException::class, 'Test error');
    });

    it('can be used in try-catch blocks', function () {
        try {
            throw new InvalidConfigurationException('Configuration is invalid');
        } catch (InvalidConfigurationException $e) {
            expect($e->getMessage())->toBe('Configuration is invalid');
            expect($e->getCode())->toBe(500);
        }
    });

    it('can be used with custom error messages', function () {
        $customMessage = 'Notifyre API key is missing from configuration';
        $exception = new InvalidConfigurationException($customMessage);

        expect($exception->getMessage())->toBe($customMessage);
    });

    it('can be used with empty message', function () {
        $exception = new InvalidConfigurationException('');

        expect($exception->getMessage())->toBe('');
        expect($exception->getCode())->toBe(500);
    });

    it('can be used with long error messages', function () {
        $longMessage = str_repeat('This is a very long error message. ', 100);
        $exception = new InvalidConfigurationException($longMessage);

        expect($exception->getMessage())->toBe($longMessage);
        expect($exception->getCode())->toBe(500);
    });
});
