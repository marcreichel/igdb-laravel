<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Console;

use Exception;
use Illuminate\Console\Command;
use InvalidArgumentException;
use MarcReichel\IGDBLaravel\Enums\Webhook\Method;
use MarcReichel\IGDBLaravel\Exceptions\InvalidWebhookMethodException;
use MarcReichel\IGDBLaravel\Models\Model;

class CreateWebhook extends Command
{
    /**
     * @var string
     */
    protected $signature = 'igdb:webhooks:create {model?} {--method=}';

    /**
     * @var string
     */
    protected $description = 'Create a webhook at IGDB.';

    public function handle(): int
    {
        $modelQuestionString = 'For which model you want to create a webhook?';

        $model = $this->argument('model') ?? $this->choice($modelQuestionString, $this->getModels());

        if (!is_string($model)) {
            throw new InvalidArgumentException(
                'Argument <comment>model</comment> has to be of type string. ' . gettype($model) . ' given.',
            );
        }

        $namespace = 'MarcReichel\IGDBLaravel\Models\\';
        $fullQualifiedName = $namespace . $model;

        if (!class_exists($fullQualifiedName)) {
            $this->line('');
            $this->error('Model "' . $model . '" does not exist.');
            $closestModel = $this->getClosestModel($model);
            if (!$closestModel) {
                return 1;
            }
            if (!$this->confirm('Did you mean <comment>' . $closestModel . '</comment>?')) {
                return 1;
            }
            $fullQualifiedName = $namespace . $closestModel;
        }

        /** @var Model $class */
        $class = $fullQualifiedName;

        $methods = ['create', 'update', 'delete'];

        $method = $this->option('method') ?? $this->choice(
            'For which event do you want to create the webhook?',
            $methods,
            'update',
        );

        if (!in_array($method, $methods, true)) {
            $this->error((new InvalidWebhookMethodException())->getMessage());

            return 1;
        }

        $mappedMethod = match ($method) {
            'create' => Method::CREATE,
            'update' => Method::UPDATE,
            'delete' => Method::DELETE,
        };

        try {
            $class::createWebhook($mappedMethod);
        } catch (Exception $e) {
            $this->error($e->getMessage());

            return 1;
        }

        $this->info('Webhook created successfully!');

        return 0;
    }

    private function getModels(): array
    {
        $pattern = '/\/(?:Model|Search|Webhook|Image)\.php$/';
        $glob = glob(__DIR__ . '/../Models/*.php');

        if (!$glob) {
            return [];
        }

        $grep = preg_grep($pattern, $glob, PREG_GREP_INVERT);

        return collect($grep ?: [])
            ->map(fn ($path) => basename($path, '.php'))
            ->toArray();
    }

    private function getClosestModel(string $model): string
    {
        return collect($this->getModels())->map(function ($m) use ($model) {
            return [
                'model' => $m,
                'levenshtein' => levenshtein($m, $model),
            ];
        })
            ->filter(fn ($m) => $m['levenshtein'] <= 5)
            ->sortBy(fn ($m) => $m['levenshtein'])
            ->map(fn ($m) => $m['model'])
            ->first();
    }
}
