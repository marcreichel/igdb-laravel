<?php

namespace MarcReichel\IGDBLaravel\Models;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use MarcReichel\IGDBLaravel\ApiHelper;
use MarcReichel\IGDBLaravel\Enums\Webhook\Method;
use MarcReichel\IGDBLaravel\Exceptions\AuthenticationException;
use MarcReichel\IGDBLaravel\Exceptions\InvalidWebhookSecretException;
use ReflectionClass;

class Webhook
{
    protected $client;

    public $id;
    public $url;
    public $category;
    public $sub_category;
    public $active;
    public $number_of_retries;
    public $secret;
    public $created_at;
    public $updated_at;

    /**
     * @throws AuthenticationException
     */
    public function __construct(...$parameters)
    {
        $this->client = new Client([
            'base_uri' => ApiHelper::IGDB_BASE_URI,
            'headers' => [
                'Accept' => 'application/json',
                'Client-ID' => config('igdb.credentials.client_id'),
                'Authorization' => 'Bearer ' . ApiHelper::retrieveAccessToken(),
            ],
        ]);

        $this->fill(...$parameters);
    }

    /**
     * @return \Illuminate\Support\Collection
     * @throws GuzzleException
     */
    public static function all(): \Illuminate\Support\Collection
    {
        $self = new static;
        return $self->mapToModel(collect(json_decode($self->client->get('webhooks')->getBody(), true)));
    }

    /**
     * @throws GuzzleException
     */
    public static function find(int $id): self
    {
        $self = new static;
        return $self->mapToModel(collect(json_decode($self->client->get('webhooks/' . $id)->getBody(), true)))->first();
    }

    /**
     * @throws GuzzleException
     */
    public function delete(): self
    {
        $self = new static;
        return $self->mapToModel(collect(json_decode($self->client->delete('webhooks/' . $this->id)->getBody(),
            true)))->first();
    }

    /**
     * @throws InvalidWebhookSecretException
     */
    public static function handle(Request $request)
    {
        self::validate($request);

        $data = json_decode($request->getContent(), true);

        $endpoint = $request->get('x_igdb_endpoint');

        if (!$endpoint) {
            return $data;
        }

        $className = Str::singular(Str::studly($endpoint));
        $fullClassName = '\\MarcReichel\\IGDBLaravel\\Models\\' . $className;

        if (!class_exists($fullClassName)) {
            return $data;
        }

        $method = $request->get('x_igdb_method');
        $entity = new $fullClassName($data);

        $reflectionClass = new ReflectionClass(Method::class);
        $allowedMethods = array_values($reflectionClass->getConstants());

        if (!$method || !in_array($method, $allowedMethods, true)) {
            return $entity;
        }

        $event = '\\MarcReichel\\IGDBLaravel\\Events\\' . $className . ucfirst(strtolower($method)) . 'd';

        if (!class_exists($event)) {
            return $entity;
        }

        $event::dispatch(new $fullClassName($data));

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

    private function fill(...$parameters): void
    {
        if ($parameters) {
            foreach ($parameters as $parameter => $value) {
                if (property_exists($this, $parameter)) {
                    if (in_array($parameter, ['created_at', 'updated_at'])) {
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
