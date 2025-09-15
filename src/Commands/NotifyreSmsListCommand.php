<?php

namespace MagicSystemsIO\Notifyre\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use MagicSystemsIO\Notifyre\Contracts\NotifyreManager;
use Symfony\Component\Console\Command\Command as CommandStatus;
use Throwable;

class NotifyreSmsListCommand extends Command
{
    public $signature = 'sms:list
        {--queries= : JSON string of query parameters (e.g. {"FromDate":1676253560,"Limit":10})}
        {--messageId= : The message ID to get specific SMS details}
        {--day : Filter for the selected number of days back (e.g. --day=4 for last 4 days)}
        {--week : Filter for the selected number of weeks back (e.g. --week=2 for last 2 weeks)}
        {--month : Filter for the selected number of months back (e.g. --month=1 for last month)}
        {--from-date= : Filter from this date (Unix timestamp)}
        {--to-date= : Filter to this date (Unix timestamp)}
        {--status= : Filter by status type}
        {--to-number= : Filter by recipient number}
        {--from-number= : Filter by sender number}
        {--search= : Search term}
        {--sort= : Sort order (asc/desc)}
        {--limit= : Number of results to return (default 10, max 100)}';

    public $description = 'List SMS messages or get a specific SMS by ID using Notifyre';

    public function handle(): int
    {
        try {
            if ($messageId = $this->option('messageId')) {
                $this->info("Getting SMS with ID: $messageId...");

                if ($result = app(NotifyreManager::class)->get($messageId)) {
                    $this->displaySMSDetails($result);
                } else {
                    $this->warn('No SMS found with that ID.');
                }

                return CommandStatus::SUCCESS;
            }

            $this->info('Getting SMS list...');

            $results = app(NotifyreManager::class)->list($this->buildQueryParams());
            if (empty($results)) {
                $this->warn('No SMS messages found.');

                return CommandStatus::SUCCESS;
            }
            $this->displaySMSList($results);

            Log::channel('notifyre')->info('testing log channel');

            return CommandStatus::SUCCESS;

        } catch (Throwable $e) {
            $this->error('Failed to get SMS messages: ' . $e->getMessage());
            $this->line('Use --help to see usage information.');

            return CommandStatus::FAILURE;
        }
    }

    private function displaySMSDetails($sms): void
    {
        $this->line('');
        $this->info('=== SMS Details ===');

        $payload = $sms->payload ?? $sms;

        $this->table(
            ['Property', 'Value'],
            [
                ['ID', $payload->id ?? 'N/A'],
                ['Friendly ID', $payload->friendlyID ?? 'N/A'],
                ['Status', $payload->status ?? 'N/A'],
                ['Campaign', $payload->campaignName ?? 'N/A'],
                ['Total Cost', $payload->totalCost ?? 'N/A'],
                ['Created', $this->formatTimestamp($payload->createdDateUtc ?? 0)],
                ['Completed', $this->formatTimestamp($payload->completedDateUtc ?? null)],
            ]
        );

        if (!empty($payload->recipients)) {
            $this->line('');
            $this->info('Recipients:');
            $this->displayRecipients($payload->recipients);
        }
    }

    private function formatTimestamp(?int $timestamp): string
    {
        if (!$timestamp) {
            return 'N/A';
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    private function displayRecipients(array $recipients): void
    {
        $recipientData = [];
        foreach ($recipients as $recipient) {
            $recipientData[] = [
                $recipient->toNumber ?? 'N/A',
                $recipient->status ?? 'N/A',
                $recipient->deliveryStatus ?? 'N/A',
                $recipient->cost ?? 'N/A',
                $recipient->messageParts ?? 'N/A',
            ];
        }

        $this->table(
            ['To Number', 'Status', 'Delivery', 'Cost', 'Parts'],
            $recipientData
        );
    }

    private function buildQueryParams(): array
    {
        $params = [];

        if ($queries = $this->option('queries')) {
            $decodedQueries = json_decode($queries, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedQueries)) {
                $params = $decodedQueries;
            } else {
                $this->warn('Invalid JSON format for --queries option. Ignoring...');
            }
        }

        $this->handleTimeBasedFilters($params);

        if ($fromDate = $this->option('from-date')) {
            $params['FromDate'] = (int) $fromDate;
        }

        if ($toDate = $this->option('to-date')) {
            $params['ToDate'] = (int) $toDate;
        }

        if ($status = $this->option('status')) {
            $params['StatusType'] = $status;
        }

        if ($toNumber = $this->option('to-number')) {
            $params['ToNumber'] = $toNumber;
        }

        if ($fromNumber = $this->option('from-number')) {
            $params['FromNumber'] = $fromNumber;
        }

        if ($search = $this->option('search')) {
            $params['Search'] = $search;
        }

        if ($sort = $this->option('sort')) {
            $params['Sort'] = $sort;
        }

        if ($limit = $this->option('limit')) {
            $params['Limit'] = min((int) $limit, 100);
        } elseif (isset($params['Limit'])) {
            $params['Limit'] = min((int) $params['Limit'], 100);
        } else {
            $params['Limit'] = 10;
        }

        return $params;
    }

    private function handleTimeBasedFilters(array &$params): void
    {
        $currentTime = time();

        if ($day = $this->option('day')) {
            $days = (int) $day;
            $fromDate = $currentTime - ($days * 24 * 60 * 60);
            $params['FromDate'] = $fromDate;
            $this->info("Filtering SMS messages from the last $days day(s)");
        }

        if ($week = $this->option('week')) {
            $weeks = (int) $week;
            $fromDate = $currentTime - ($weeks * 7 * 24 * 60 * 60);
            $params['FromDate'] = $fromDate;
            $this->info("Filtering SMS messages from the last $weeks week(s)");
        }

        if ($month = $this->option('month')) {
            $months = (int) $month;
            $fromDate = $currentTime - ($months * 30 * 24 * 60 * 60);
            $params['FromDate'] = $fromDate;
            $this->info("Filtering SMS messages from the last $months month(s)");
        }
    }

    private function displaySMSList(array $smsList): void
    {
        $this->line('');
        $this->info('=== SMS Messages ===');

        $tableData = [];
        foreach ($smsList as $sms) {
            $payload = $sms->payload ?? $sms;
            $tableData[] = [
                $payload->id ?? 'N/A',
                $payload->friendlyID ?? 'N/A',
                $payload->status ?? 'N/A',
                count($payload->recipients ?? []),
                $payload->totalCost ?? 'N/A',
                $this->formatTimestamp($payload->createdDateUtc ?? 0),
            ];
        }

        $this->table(
            ['ID', 'Friendly ID', 'Status', 'Recipients', 'Cost', 'Created'],
            $tableData
        );

        $this->line('');
        $this->info('Total: ' . count($smsList) . ' SMS messages');
    }
}
