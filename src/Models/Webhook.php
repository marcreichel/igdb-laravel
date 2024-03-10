<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use JsonException;
use MarcReichel\IGDBLaravel\ApiHelper;
use MarcReichel\IGDBLaravel\Enums\Webhook\Category;
use MarcReichel\IGDBLaravel\Enums\Webhook\Method;
use MarcReichel\IGDBLaravel\Exceptions\AuthenticationException;
use MarcReichel\IGDBLaravel\Exceptions\InvalidWebhookSecretException;

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
        $this->client = Http::withOptions([
            'base_uri' => ApiHelper::IGDB_BASE_URI,
        ])
            ->withHeaders([
                'Accept' => 'application/json',
                'Client-ID' => config('igdb.credentials.client_id'),
                'Authorization' => 'Bearer ' . ApiHelper::retrieveAccessToken(),
            ]);

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
        if (!$this->id) {
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

        $className = Str::singular(Str::studly($endpoint));
        $fullClassName = 'MarcReichel\\IGDBLaravel\\Models\\' . $className;

        if (!class_exists($fullClassName)) {
            return $data;
        }

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
        $categories = collect(Category::cases())
            ->mapWithKeys(fn (Category $category) => [(string) $category->value => $category->name]);

        $category = $categories->get($this->category);

        if (!is_string($category)) {
            return (string) $this->category;
        }

        return $category;
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
        if ($parameters) {
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
        return $collection->map(function (array $item) {
            $webhook = new self(...$item);

            unset($webhook->client);

            return $webhook;
        });
    }
}
