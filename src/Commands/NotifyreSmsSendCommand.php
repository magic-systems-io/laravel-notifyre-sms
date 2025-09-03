<?php

namespace MagicSystemsIO\Notifyre\Commands;

use Exception;
use Illuminate\Console\Command;
use MagicSystemsIO\Notifyre\DTO\SMS\Recipient;
use MagicSystemsIO\Notifyre\DTO\SMS\RequestBody;
use MagicSystemsIO\Notifyre\Enums\NotifyreRecipientTypes;
use MagicSystemsIO\Notifyre\Services\NotifyreService;
use Symfony\Component\Console\Command\Command as CommandStatus;
use Throwable;

class NotifyreSmsSendCommand extends Command
{
    public $signature = 'sms:send 
		{--r|recipient=* : The number and optional type, e.g. +123456789:mobile_number,+987654321:contact} 
		{--m|message= : The message that will be sent}';

    public $description = 'Send an SMS to a specified phone number using Notifyre';

    public function handle(): int
    {
        try {
            $this->info('Sending SMS...');
            NotifyreService::send(new RequestBody(
                body: $this->parseMessage(),
                recipients: $this->parseRecipients(),
            ));
            $this->info('SMS sent successfully!');

            return  CommandStatus::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Failed to send SMS: ' . $e->getMessage());
            $this->line('Use --help to see usage information.');

            return  CommandStatus::FAILURE;
        }
    }

    /**
     * @throws Exception
     */
    private function parseMessage(): string
    {
        return $this->option('message') ?? throw new Exception('You must provide a message to send.');
    }

    /**
     * @throws Exception
     * @return Recipient[]
     */
    private function parseRecipients(): array
    {
        $argRecipients = $this->option('recipient');
        if (!$argRecipients) {
            throw new Exception('You must provide a recipient to send the SMS to.');
        }

        return array_map(function (string $recipient): Recipient {
            [$number, $type] = explode(':', $recipient, 2) + [null, null];
            $type = $type ?: NotifyreRecipientTypes::MOBILE_NUMBER->value;

            return new Recipient(type: $type, value: $number);

        }, $argRecipients);
    }
}
