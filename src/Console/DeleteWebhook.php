<?php

namespace MarcReichel\IGDBLaravel\Console;

use Illuminate\Console\Command;
use MarcReichel\IGDBLaravel\Models\Webhook;

class DeleteWebhook extends Command
{
    /**
     * @var string
     */
    protected $signature = 'igdb:webhooks:delete {id?} {--A|all}';

    /**
     * @var string
     */
    protected $description = 'Delete a webhook at IGDB.';

    /**
     * @return int
     */
    public function handle(): int
    {
        $id = (int)$this->argument('id');

        if ($id) {
            return $this->deleteOne($id);
        }

        if ($this->option('all')) {
            return $this->deleteAll();
        }

        return 0;
    }

    /**
     * @param  int  $id
     *
     * @return int
     */
    private function deleteOne(int $id): int
    {
        $webhook = Webhook::find($id);

        if (!$webhook instanceof Webhook) {
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
