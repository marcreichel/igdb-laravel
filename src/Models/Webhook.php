<?php

namespace MarcReichel\IGDBLaravel\Models;

use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use JsonException;
use MarcReichel\IGDBLaravel\ApiHelper;
use MarcReichel\IGDBLaravel\Enums\Webhook\Category;
use MarcReichel\IGDBLaravel\Enums\Webhook\Method;
use MarcReichel\IGDBLaravel\Exceptions\AuthenticationException;
use MarcReichel\IGDBLaravel\Exceptions\InvalidWebhookSecretException;
use MarcReichel\IGDBLaravel\Interfaces\WebhookInterface;
use ReflectionClass;

class Webhook implements WebhookInterface
{
    /**
     * @var PendingRequest
     */
    private PendingRequest $client;

    /**
     * @var int
     */
    public int $id;

    /**
     * @var string
     */
    public string $url;

    /**
     * @var int
     */
    public int $category;

    /**
     * @var int
     */
    public int $sub_category;

    /**
     * @var bool
     */
    public bool $active;

    /**
     * @var int
     */
    public int $number_of_retries;

    /**
     * @var string
     */
    public string $secret;

    /**
     * @var string
     */
    public string $created_at;

    /**
     * @var string
     */
    public string $updated_at;

    /**
     * @param  mixed  ...$parameters
     *
     * @throws AuthenticationException
     */
    public function __construct(mixed ...$parameters)
    {
        $this->client = Http::withOptions([
            'base_uri' => ApiHelper::IGDB_BASE_URI,
        ])
            ->withHeaders([
                'Accept' => 'application/json',
                'Client-ID' => config('igdb.credentials.client_id'),
                'Authorization' => 'Bearer '.ApiHelper::retrieveAccessToken(),
            ]);

        $this->fill(...$parameters);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public static function all(): \Illuminate\Support\Collection
    {
        $self = new static;
        $response = $self->client->get('webhooks');

        if ($response->failed()) {
            return new \Illuminate\Support\Collection();
        }
        return $self->mapToModel(collect($response->json()));
    }

    /**
     * @param  int  $id
     *
     * @return mixed
     */
    public static function find(int $id): mixed
    {
        $self = new static;
        $response = $self->client->get('webhooks/'.$id);
        if ($response->failed()) {
            return null;
        }
        return $self->mapToModel(collect($response->json()))->first();
    }

    /**
     * @return mixed
     */
    public function delete(): mixed
    {
        if (!$this->id) {
            return false;
        }

        $self = new static;

        $response = $self->client->delete('webhooks/'.$this->id)->json();

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

        $endpoint = (string) $request->route('model');

        if (!$endpoint) {
            return $data;
        }

        $className = Str::singular(Str::studly($endpoint));
        $fullClassName = '\\MarcReichel\\IGDBLaravel\\Models\\'.$className;

        if (!class_exists($fullClassName)) {
            return $data;
        }

        $method = (string) $request->route('method');
        $entity = new $fullClassName($data);

        $reflectionClass = new ReflectionClass(Method::class);
        $allowedMethods = array_values($reflectionClass->getConstants());

        if (!$method || !in_array($method, $allowedMethods, true)) {
            return $entity;
        }

        $event = '\\MarcReichel\\IGDBLaravel\\Events\\'.$className.ucfirst(strtolower($method)).'d';

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

    /**
     * @return string
     */
    public function getModel(): string
    {
        $reflectionCategory = new ReflectionClass(Category::class);
        $categories = collect($reflectionCategory->getConstants())->flip();

        $category = $categories->get((string) $this->category);

        if (!is_string($category)) {
            return (string) $this->category;
        }

        return $category;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        $reflectionMethod = new ReflectionClass(Method::class);
        $methods = collect($reflectionMethod->getConstants())->values();

        $method = $methods->get((string) $this->sub_category);

        if (!is_string($method)) {
            return (string) $this->sub_category;
        }

        return $method;
    }

    #[ArrayShape([
        'id' => "int",
        'url' => "string",
        'category' => "int|mixed",
        'sub_category' => "int|mixed",
        'number_of_retries' => "int",
        'active' => "bool",
    ])]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'category' => $this->getModel(),
            'sub_category' => $this->getMethod(),
            'number_of_retries' => $this->number_of_retries,
            'active' => $this->active,
        ];
    }

    /**
     * @param  mixed  ...$parameters
     */
    private function fill(mixed ...$parameters): void
    {
        if ($parameters) {
            foreach ($parameters as $parameter => $value) {
                if (property_exists($this, (string) $parameter)) {
                    if (is_string($value) && in_array($parameter, ['created_at', 'updated_at'])) {
                        $this->{$parameter} = new Carbon($value);
                    } else {
                        $this->{$parameter} = $value;
                    }
                }
            }
        }
    }

    private function mapToModel(\Illuminate\Support\Collection $collection): \Illuminate\Support\Collection
    {
        return $collection->map(function ($item) {
            $webhook = new self(...$item);

            unset($webhook->client);

            return $webhook;
        });
    }
}
