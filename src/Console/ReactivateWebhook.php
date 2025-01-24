<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Console;

use Illuminate\Console\Command;
use MarcReichel\IGDBLaravel\Exceptions\AuthenticationException;
use MarcReichel\IGDBLaravel\Exceptions\WebhookSecretMissingException;
use MarcReichel\IGDBLaravel\Models\Model;
use MarcReichel\IGDBLaravel\Models\Webhook;

class ReactivateWebhook extends Command
{
    /**
     * @var string
     */
    protected $signature = 'igdb:webhooks:reactivate {id}';

    /**
     * @var string
     */
    protected $description = 'Reactivate an inactive webhook.';

    public function handle(): int
    {
        /** @var Webhook|null $webhook */
        $webhook = Webhook::find((int) $this->argument('id'));

        if (!$webhook) {
            $this->error('Webhook not found.');

            return self::FAILURE;
        }

        if ($webhook->active) {
            $this->info('Webhook does not need to be reactivated.');

            return self::SUCCESS;
        }

        $model = $webhook->getModel();
        $method = $webhook->getMethod();

        $fullQualifiedName = 'MarcReichel\\IGDBLaravel\\Models\\' . $model;

        if (!class_exists($fullQualifiedName)) {
            $this->error('Model not found.');

            return self::FAILURE;
        }

        /** @var class-string<Model> $class */
        $class = $fullQualifiedName;

        try {
            $class::createWebhook($method);
        } catch (AuthenticationException | WebhookSecretMissingException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info('Webhook reactivated.');

        return self::SUCCESS;
    }
}
