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
                return self::FAILURE;
            }
            if (!$this->confirm('Did you mean <comment>' . $closestModel . '</comment>?')) {
                return self::FAILURE;
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

            return self::FAILURE;
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

            return self::FAILURE;
        }

        $this->info('Webhook created successfully!');

        return self::SUCCESS;
    }

    private function getModels(): array
    {
        $glob = glob(__DIR__ . '/../Models/*.php') ?? [];

        $pattern = '/\/(?:Model|Search|Webhook|Image)\.php$/';
        $grep = preg_grep($pattern, $glob, PREG_GREP_INVERT);

        return collect($grep ?: [])
            ->map(fn (string $path): string => basename($path, '.php'))
            ->toArray();
    }

    private function getClosestModel(string $model): ?string
    {
        return collect($this->getModels())->map(fn (string $m): array => [
            'model' => $m,
            'levenshtein' => levenshtein($m, $model),
        ])
            ->filter(fn (array $m) => $m['levenshtein'] <= 5)
            ->sortBy(fn (array $m) => $m['levenshtein'])
            ->map(fn (array $m) => $m['model'])
            ->first();
    }
}
