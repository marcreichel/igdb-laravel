<?php

namespace MarcReichel\IGDBLaravel\Console;

use Illuminate\Console\Command;
use MarcReichel\IGDBLaravel\Exceptions\AuthenticationException;
use MarcReichel\IGDBLaravel\Exceptions\InvalidWebhookMethodException;
use MarcReichel\IGDBLaravel\Exceptions\WebhookSecretMissingException;
use MarcReichel\IGDBLaravel\Models\Model;
use MarcReichel\IGDBLaravel\Models\Webhook;

class ReactivateWebhook extends Command
{
    protected $signature = 'igdb:webhooks:reactivate {id}';

    protected $description = 'Reactivate an inactive webhook.';

    public function handle(): int
    {
        /** @var Webhook|null $webhook */
        $webhook = Webhook::find($this->argument('id'));

        if (!$webhook) {
            $this->error('Webhook not found.');
            return 1;
        }

        if ($webhook->active) {
            $this->info('Webhook does not need to be reactivated.');
            return 0;
        }

        $model = $webhook->getModel();
        $method = $webhook->getMethod();

        $fullQualifiedName = 'MarcReichel\\IGDBLaravel\\Models\\' . $model;

        if (!class_exists($fullQualifiedName)) {
            $this->error('Model not found.');

            return 1;
        }

        /** @var Model $class */
        $class = $fullQualifiedName;

        try {
            $class::createWebhook($method);
        } catch (AuthenticationException | InvalidWebhookMethodException | WebhookSecretMissingException $e) {
            $this->error($e->getMessage());

            return 1;
        }

        $this->info('Webhook reactivated.');

        return 0;
    }
}
