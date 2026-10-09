<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use JsonException;
use MarcReichel\IGDBLaravel\ApiHelper;
use MarcReichel\IGDBLaravel\Enums\Webhook\Method;
use MarcReichel\IGDBLaravel\Exceptions\AuthenticationException;
use MarcReichel\IGDBLaravel\Exceptions\InvalidWebhookSecretException;
use ReflectionClass;

class Webhook
{
    public int $id;
    public string $url;
    public int $category;
    public int $sub_category;
    public bool $active;
    public int $number_of_retries;
    public string $secret;
    public string $created_at;
    public string $updated_at;

    private PendingRequest $client;

    /**
     * @throws AuthenticationException
     */
    final public function __construct(mixed ...$parameters)
    {
        $this->client = ApiHelper::client();

        $this->fill(...$parameters);
    }

    public static function all(): \Illuminate\Support\Collection
    {
        $self = new static();
        $response = $self->client->get('webhooks');

        if ($response->failed()) {
            return \Illuminate\Support\Collection::make();
        }

        return $self->mapToModel(collect($response->json()));
    }

    public static function find(int $id): ?self
    {
        $self = new static();
        $response = $self->client->get('webhooks/' . $id);
        if ($response->failed()) {
            return null;
        }

        return $self->mapToModel(collect($response->json()))->first();
    }

    public function delete(): mixed
    {
        if ($this->id === 0) {
            return false;
        }

        $self = new static();

        $response = $self->client->delete('webhooks/' . $this->id)->json();

        if (!$response) {
            return false;
        }

        return $self->mapToModel(collect([$response]))->first();
    }

    /**
     * @throws InvalidWebhookSecretException|JsonException
     */
    public static function handle(Request $request): mixed
    {
        self::validate($request);

        $data = json_decode((string) $request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        /** @var string $endpoint */
        $endpoint = $request->route('model');

        if (!$endpoint) {
            return $data;
        }

        $fullClassName = self::modelForEndpoint($endpoint);

        if (!$fullClassName) {
            return $data;
        }

        $className = class_basename($fullClassName);

        /** @var string $method */
        $method = $request->route('method');
        $entity = new $fullClassName($data);

        $allowedMethods = collect(Method::cases())
            ->map(static fn (Method $method) => $method->value)
            ->toArray();

        if (!$method || !in_array($method, $allowedMethods, true)) {
            return $entity;
        }

        $event = 'MarcReichel\\IGDBLaravel\\Events\\' . $className . ucfirst(strtolower($method)) . 'd';

        if (!class_exists($event)) {
            return $entity;
        }

        $event::dispatch(new $fullClassName($data), $request);

        return $entity;
    }

    /**
     * @throws InvalidWebhookSecretException
     */
    public static function validate(Request $request): void
    {
        $secretHeader = $request->header('X-Secret');

        if ($secretHeader === config('igdb.webhook_secret')) {
            return;
        }

        throw new InvalidWebhookSecretException();
    }

    public function getModel(): string
    {
        // Our callback URLs end in /{endpoint}/{method}, see Model::createWebhook().
        $segments = explode('/', trim((string) parse_url($this->url, PHP_URL_PATH), '/'));
        $model = self::modelForEndpoint($segments[count($segments) - 2] ?? '');

        return $model ? class_basename($model) : (string) $this->category;
    }

    private static function modelForEndpoint(string $endpoint): ?string
    {
        // Match on the model's own endpoint; reversing the string breaks on e.g. character_species or *_v2.
        return collect(glob(__DIR__ . '/*.php') ?: [])
            ->map(static fn (string $file): string => __NAMESPACE__ . '\\' . basename($file, '.php'))
            ->first(static fn (string $class): bool => is_subclass_of($class, Model::class)
                && (new ReflectionClass($class))->isInstantiable()
                && (new $class())->getEndpoint() === $endpoint);
    }

    public function getMethod(): Method
    {
        return match ($this->sub_category) {
            1 => Method::DELETE,
            2 => Method::UPDATE,
            default => Method::CREATE,
        };
    }

    public function getSubCategory(): int
    {
        return $this->sub_category;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'category' => $this->category,
            'sub_category' => $this->sub_category,
            'number_of_retries' => $this->number_of_retries,
            'active' => $this->active,
        ];
    }

    private function fill(mixed ...$parameters): void
    {
        if ($parameters !== []) {
            foreach ($parameters as $parameter => $value) {
                if (property_exists($this, (string) $parameter)) {
                    if (in_array($parameter, ['created_at', 'updated_at'])) {
                        $this->{$parameter} = (string) new Carbon($value);
                    } else {
                        $this->{$parameter} = $value;
                    }
                }
            }
        }
    }

    private function mapToModel(\Illuminate\Support\Collection $collection): \Illuminate\Support\Collection
    {
        return $collection->map(static function (array $item) {
            $webhook = new self(...$item);

            unset($webhook->client);

            return $webhook;
        });
    }
}
