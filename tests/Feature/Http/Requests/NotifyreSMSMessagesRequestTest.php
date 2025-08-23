<?php

namespace MagicSystemsIO\Notifyre\Tests\Feature\Http\Requests;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use MagicSystemsIO\Notifyre\Http\Requests\NotifyreSMSMessagesRequest;

describe('NotifyreSMSMessagesRequest', function () {
    beforeEach(function () {
        $this->request = new NotifyreSMSMessagesRequest();
    });

    describe('authorization', function () {
        it('always returns true', function () {
            $result = $this->request->authorize();
            expect($result)->toBeTrue();
        });
    });

    describe('validation rules', function () {
        it('has correct validation rules', function () {
            $rules = $this->request->rules();

            expect($rules)->toHaveKey('body')
                ->and($rules)->toHaveKey('sender')
                ->and($rules)->toHaveKey('recipients')
                ->and($rules)->toHaveKey('recipients.*')
                ->and($rules)->toHaveKey('recipients.*.type')
                ->and($rules)->toHaveKey('recipients.*.value')
                ->and($rules)->toHaveKey('persist');
        });

        it('body field is required, string, and max 160 characters', function () {
            $rules = $this->request->rules();
            $bodyRules = $rules['body'];

            expect($bodyRules)->toContain('required')
                ->and($bodyRules)->toContain('string')
                ->and($bodyRules)->toContain('max:160');
        });

        it('sender field is nullable, string, and max 255 characters', function () {
            $rules = $this->request->rules();
            $senderRules = $rules['sender'];

            expect($senderRules)->toContain('nullable')
                ->and($senderRules)->toContain('string')
                ->and($senderRules)->toContain('max:255');
        });

        it('recipients field is required and array', function () {
            $rules = $this->request->rules();
            $recipientsRules = $rules['recipients'];

            expect($recipientsRules)->toContain('required')
                ->and($recipientsRules)->toContain('array')
                ->and($recipientsRules)->toContain('min:1');
        });

        it('recipients.* field is required and array', function () {
            $rules = $this->request->rules();
            $recipientItemRules = $rules['recipients.*'];

            expect($recipientItemRules)->toContain('required')
                ->and($recipientItemRules)->toContain('array')
            ->and($recipientItemRules)->toContain('min:1');
        });

        it('recipients.*.type field is required, string, and has enum validation', function () {
            $rules = $this->request->rules();
            $typeRules = $rules['recipients.*.type'];

            expect($typeRules)->toContain('required')
                ->and($typeRules)->toContain('string');

            $enumRule = null;
            foreach ($typeRules as $rule) {
                if (is_object($rule)) {
                    $enumRule = $rule;
                    break;
                }
            }

            expect($enumRule)->toBeInstanceOf(Enum::class);
        });

        it('recipients.*.value field is required, string, and max 255 characters', function () {
            $rules = $this->request->rules();
            $valueRules = $rules['recipients.*.value'];

            expect($valueRules)->toContain('required')
                ->and($valueRules)->toContain('string')
                ->and($valueRules)->toContain('max:255');
        });
    });

    describe('validation scenarios', function () {
        it('passes validation with valid data', function () {
            $data = [
                'body' => 'Test message',
                'sender' => 'TestApp',
                'recipients' => [
                    ['type' => 'virtual_mobile_number', 'value' => '+1234567890'],
                    ['type' => 'contact', 'value' => 'contact123'],
                ],
            ];

            $validator = Validator::make($data, $this->request->rules());
            $isValid = $validator->passes();

            expect($isValid)->toBeTrue();
        });

        it('passes validation without sender', function () {
            $data = [
                'body' => 'Test message',
                'recipients' => [
                    ['type' => 'virtual_mobile_number', 'value' => '+1234567890'],
                ],
            ];

            $validator = Validator::make($data, $this->request->rules());
            $isValid = $validator->passes();

            expect($isValid)->toBeTrue();
        });

        it('fails validation when body is missing', function () {
            $data = [
                'sender' => 'TestApp',
                'recipients' => [
                    ['type' => 'virtual_mobile_number', 'value' => '+1234567890'],
                ],
            ];

            $validator = Validator::make($data, $this->request->rules());
            $isValid = $validator->passes();

            expect($isValid)->toBeFalse()
                ->and($validator->errors()->has('body'))->toBeTrue();
        });

        it('fails validation when body is empty', function () {
            $data = [
                'body' => '',
                'sender' => 'TestApp',
                'recipients' => [
                    ['type' => 'virtual_mobile_number', 'value' => '+1234567890'],
                ],
            ];

            $validator = Validator::make($data, $this->request->rules());
            $isValid = $validator->passes();

            expect($isValid)->toBeFalse()
                ->and($validator->errors()->has('body'))->toBeTrue();
        });

        it('fails validation when body is too long', function () {
            $data = [
                'body' => str_repeat('a', 161), // 161 characters
                'sender' => 'TestApp',
                'recipients' => [
                    ['type' => 'virtual_mobile_number', 'value' => '+1234567890'],
                ],
            ];

            $validator = Validator::make($data, $this->request->rules());
            $isValid = $validator->passes();

            expect($isValid)->toBeFalse()
                ->and($validator->errors()->has('body'))->toBeTrue();
        });

        it('fails validation when recipients is missing', function () {
            $data = [
                'body' => 'Test message',
                'sender' => 'TestApp',
            ];

            $validator = Validator::make($data, $this->request->rules());
            $isValid = $validator->passes();

            expect($isValid)->toBeFalse()
                ->and($validator->errors()->has('recipients'))->toBeTrue();
        });

        it('fails validation when recipients is empty array', function () {
            $data = [
                'body' => 'Test message',
                'sender' => 'TestApp',
                'recipients' => [],
            ];

            $validator = Validator::make($data, $this->request->rules());
            $isValid = $validator->passes();

            expect($isValid)->toBeFalse()
                ->and($validator->errors()->has('recipients'))->toBeTrue();
        });

        it('fails validation when recipient type is invalid', function () {
            $data = [
                'body' => 'Test message',
                'sender' => 'TestApp',
                'recipients' => [
                    ['type' => 'invalid_type', 'value' => '+1234567890'],
                ],
            ];

            $validator = Validator::make($data, $this->request->rules());
            $isValid = $validator->passes();

            expect($isValid)->toBeFalse()
                ->and($validator->errors()->has('recipients.0.type'))->toBeTrue();
        });

        it('fails validation when recipient value is missing', function () {
            $data = [
                'body' => 'Test message',
                'sender' => 'TestApp',
                'recipients' => [
                    ['type' => 'virtual_mobile_number'],
                ],
            ];

            $validator = Validator::make($data, $this->request->rules());
            $isValid = $validator->passes();

            expect($isValid)->toBeFalse()
                ->and($validator->errors()->has('recipients.0.value'))->toBeTrue();
        });

        it('fails validation when recipient value is empty', function () {
            $data = [
                'body' => 'Test message',
                'sender' => 'TestApp',
                'recipients' => [
                    ['type' => 'virtual_mobile_number', 'value' => ''],
                ],
            ];

            $validator = Validator::make($data, $this->request->rules());
            $isValid = $validator->passes();

            expect($isValid)->toBeFalse()
                ->and($validator->errors()->has('recipients.0.value'))->toBeTrue();
        });

        it('fails validation when recipient value is too long', function () {
            $data = [
                'body' => 'Test message',
                'sender' => 'TestApp',
                'recipients' => [
                    ['type' => 'virtual_mobile_number', 'value' => str_repeat('a', 256)],
                ],
            ];

            $validator = Validator::make($data, $this->request->rules());
            $isValid = $validator->passes();

            expect($isValid)->toBeFalse()
                ->and($validator->errors()->has('recipients.0.value'))->toBeTrue();
        });

        it('fails validation when sender is too long', function () {
            $data = [
                'body' => 'Test message',
                'sender' => str_repeat('a', 256),
                'recipients' => [
                    ['type' => 'virtual_mobile_number', 'value' => '+1234567890'],
                ],
            ];

            $validator = Validator::make($data, $this->request->rules());
            $isValid = $validator->passes();

            expect($isValid)->toBeFalse()
                ->and($validator->errors()->has('sender'))->toBeTrue();
        });

        it('fails validation when persist is not a boolean', function () {
            $data = [
                'body' => 'Test message',
                'sender' => 'TestApp',
                'recipients' => [
                    ['type' => 'virtual_mobile_number', 'value' => '+1234567890'],
                ],
                'persist' => 'not_a_boolean',
            ];

            $validator = Validator::make($data, $this->request->rules());
            $isValid = $validator->passes();

            expect($isValid)->toBeFalse()
                ->and($validator->errors()->has('persist'))->toBeTrue();
        });
    });

    describe('enum validation', function () {
        it('accepts valid recipient types', function () {
            $validTypes = ['virtual_mobile_number', 'contact', 'group'];

            foreach ($validTypes as $type) {
                $data = [
                    'body' => 'Test message',
                    'recipients' => [
                        ['type' => $type, 'value' => 'test_value'],
                    ],
                ];

                $validator = Validator::make($data, $this->request->rules());
                $isValid = $validator->passes();

                expect($isValid)->toBeTrue();
            }
        });

        it('rejects invalid recipient types', function () {
            $invalidTypes = ['invalid', 'phone', 'email', ''];

            foreach ($invalidTypes as $type) {
                $data = [
                    'body' => 'Test message',
                    'recipients' => [
                        ['type' => $type, 'value' => 'test_value'],
                    ],
                ];

                $validator = Validator::make($data, $this->request->rules());
                $isValid = $validator->passes();

                expect($isValid)->toBeFalse()
                    ->and($validator->errors()->has('recipients.0.type'))->toBeTrue();
            }
        });
    });
});
