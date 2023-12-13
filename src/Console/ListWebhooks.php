<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Console;

use Illuminate\Console\Command;
use MarcReichel\IGDBLaravel\Models\Webhook;

class ListWebhooks extends Command
{
    protected $signature = 'igdb:webhooks';

    protected $description = 'List all your registered webhooks at IGDB.';

    public function handle(): int
    {
        $webhooks = Webhook::all();

        if (!$webhooks->count()) {
            $this->warn('You do not have any registered webhooks.');

            return 1;
        }
        $this->table(
            ['ID', 'URL', 'Model', 'Method', 'Retries', 'Active'],
            $webhooks->map(function (Webhook $webhook) {
                $data = $webhook->toArray();

                $data['active'] = $data['active'] ? '  ✅  ' : '  ❌  ';

                return $data;
            })->sortBy('id')->toArray(),
        );

        return 0;
    }
}
