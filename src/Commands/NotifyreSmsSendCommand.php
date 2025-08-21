<?php

namespace Arbi\Notifyre\Commands;

use Arbi\Notifyre\Contracts\NotifyreServiceInterface;
use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Exception;
use Illuminate\Console\Command;

class NotifyreSmsSendCommand extends Command
{
    public $signature = 'sms:send 
                        {--sender= : The number the SMS will be sent from}
                        {--recipient= : The number the SMS will be sent to} 
                        {--message= : The message that will be sent}';

    public $description = 'Send an SMS to a specified phone number using Notifyre';

    public function __construct(
        private readonly NotifyreServiceInterface $notifyreService
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $arguments = $this->retrieveArguments();
        if (empty($arguments)) {
            return;
        }
        [$sender, $recipient, $message] = $arguments;

        try {
            $this->info('Sending SMS...');

            $this->notifyreService->send(new RequestBodyDTO(
                body: $message,
                sender: $sender,
                recipients: [
                    new Recipient('mobile_number', $recipient),
                ]
            ));

            $this->info('SMS sent successfully!');
        } catch (Exception $e) {
            $this->error('Failed to send SMS: ' . $e->getMessage());
        }
    }

    /**
     * @return array{sender: string, recipient: string, message: string}|array{}
     */
    private function retrieveArguments(): array
    {
        $sender = $this->option('sender');
        $recipient = $this->option('recipient');
        $message = $this->option('message');

        if (!$message) {
            $this->error('You must provide a message to send.');
            $this->line('Usage: sms:send --sender=+123456789 --recipient=+123456789 --message="Hello World!"');

            return [];
        }

        $sender = $sender ?: config('notifyre.default_sender');
        $recipient = $recipient ?: config('notifyre.default_recipient');

        if (!$sender || !$recipient) {
            $this->error('Unable to determine sender or recipient. Check your configuration.');

            return [];
        }

        return [$sender, $recipient, $message];
    }
}
