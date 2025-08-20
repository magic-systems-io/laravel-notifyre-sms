<?php

namespace Arbi\Notifyre\Commands;

use Arbi\Notifyre\DTO\SMS\Recipient;
use Arbi\Notifyre\DTO\SMS\RequestBodyDTO;
use Arbi\Notifyre\Services\NotifyreService;
use Exception;
use Illuminate\Console\Command;

class NotifyreSmsSendCommand extends Command
{
    protected $signature = 'sms:send {sender? : The number the SMS will be sent from} {recipient? : The number the SMS will be sent to} {message? : The message that will be sent}'; //this name may conflict with other packages, consider renaming it

    protected $description = 'Send an SMS to a specified phone number using Notifyre';

    public function __construct(
        private readonly NotifyreService $notifyreService
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
        $sender = $this->argument('sender');
        $recipient = $this->argument('recipient');
        $message = $this->argument('message');

        if (!$message) {
            $this->error('You must provide a message to send.');
            $this->line('Usage: sms:send {sender?} {recipient?} {message?}');

            return [];
        }

        if ($sender && !$recipient) {
            $recipient = config('notifyre.default_recipient');
        } elseif (!$sender && $recipient) {
            $sender = config('notifyre.default_sender');
        } elseif (!$sender && !$recipient) {
            $sender = config('notifyre.default_sender');
            $recipient = config('notifyre.default_recipient');
        }

        if (!$sender || !$recipient) {
            $this->error('Unable to determine sender or recipient. Check your configuration.');

            return [];
        }

        return [$sender, $recipient, $message];
    }
}
