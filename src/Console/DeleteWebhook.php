<?php

namespace MarcReichel\IGDBLaravel\Console;

use Illuminate\Console\Command;
use MarcReichel\IGDBLaravel\Models\Webhook;

class DeleteWebhook extends Command
{
    protected $signature = 'igdb:webhooks:delete {id?} {--A|all}';

    protected $description = 'Delete a webhook at IGDB.';

    public function handle(): int
    {
        $id = $this->argument('id');

        if ($id) {
            return $this->deleteOne($id);
        }

        if ($this->option('all')) {
            return $this->deleteAll();
        }

        return 0;
    }

    /**
     * @param $id
     *
     * @return int
     */
    private function deleteOne($id): int
    {
        /** @var Webhook $webhook */
        $webhook = Webhook::find($id);

        if (!$webhook) {
            $this->error('Webhook not found.');
            return 1;
        }

        if (!$webhook->delete()) {
            $this->error('Webhook could not be deleted.');
            return 1;
        }

        $this->info('Webhook deleted.');

        return 0;
    }

    /**
     * @return int
     */
    private function deleteAll(): int
    {
        $webhooks = Webhook::all();

        if (!$webhooks->count()) {
            $this->info('You do not have any registered webhooks.');

            return 1;
        }

        $this->comment('Deleting all your registered webhooks ...');

        $this->withProgressBar($webhooks, function (Webhook $webhook) {
            $webhook->delete();
        });

        $this->info('');

        $this->info('All Webhooks deleted.');

        return 0;
    }
}
