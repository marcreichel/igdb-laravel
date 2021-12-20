<?php

namespace MarcReichel\IGDBLaravel\Models;

use Error;
use ArrayAccess;
use Carbon\Carbon;
use BadMethodCallException;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use MarcReichel\IGDBLaravel\ApiHelper;
use MarcReichel\IGDBLaravel\Builder;
use Illuminate\Contracts\Support\{Jsonable, Arrayable};
use MarcReichel\IGDBLaravel\Traits\{HasAttributes, HasRelationships};
use MarcReichel\IGDBLaravel\Enums\Webhook\Method;
use MarcReichel\IGDBLaravel\Exceptions\AuthenticationException;
use MarcReichel\IGDBLaravel\Exceptions\InvalidParamsException;
use MarcReichel\IGDBLaravel\Exceptions\InvalidWebhookMethodException;
use MarcReichel\IGDBLaravel\Exceptions\WebhookSecretMissingException;
use MarcReichel\IGDBLaravel\Interfaces\ModelInterface;
use ReflectionClass;
use ReflectionException;

/**
 * Class Model
 *
 * @method static Builder select(mixed $fields)
 * @method static Builder limit(int $limit)
 * @method static Builder take(int $limit)
 * @method static Builder offset(int $limit)
 * @method static Builder skip(int $limit)
 * @method static Builder forPage(int $page, int $perPage = 10)
 * @method static Builder search(string $query)
 * @method static Builder fuzzySearch(mixed $key, string $query, bool $caseSensitive = false, string $boolean = '&')
 * @method static Builder orFuzzySearch(mixed $key, string $query, bool $caseSensitive = false, string $boolean = '|')
 * @method static Builder where(mixed $key, mixed|null $operator = null, mixed|null $value = null, string $boolean = '&')
 * @method static Builder orWhere(mixed $key, mixed|null $operator = null, mixed|null $value = null, string $boolean = '|')
 * @method static Builder whereLike(string $key, string $value, bool $caseSensitive = true, string $boolean = '&')
 * @method static Builder orWhereLike(string $key, string $value, bool $caseSensitive = true, string $boolean = '|')
 * @method static Builder whereNotLike(string $key, string $value, bool $caseSensitive = true, string $boolean = '&')
 * @method static Builder orWhereNotLike(string $key, string $value, bool $caseSensitive = true, string $boolean = '|')
 * @method static Builder whereIn(string $key, array $values, string $boolean = '&', string $operator = '=', string $prefix = '(', string $suffix = ')')
 * @method static Builder orWhereIn(string $key, array $values, string $boolean = '|', string $operator = '=', string $prefix = '(', string $suffix = ')')
 * @method static Builder whereInAll(string $key, array $values, string $boolean = '&', string $operator = '=', string $prefix = '[', string $suffix = ']')
 * @method static Builder orWhereInAll(string $key, array $values, string $boolean = '|', string $operator = '=', string $prefix = '[', string $suffix = ']')
 * @method static Builder whereInExact(string $key, array $values, string $boolean = '&', string $operator = '=', string $prefix = '{', string $suffix = '}')
 * @method static Builder orWhereInExact(string $key, array $values, string $boolean = '|', string $operator = '=', string $prefix = '{', string $suffix = '}')
 * @method static Builder whereNotIn(string $key, array $values, string $boolean = '&', string $operator = '!=', string $prefix = '(', string $suffix = ')')
 * @method static Builder orWhereNotIn(string $key, array $values, string $boolean = '|', string $operator = '!=', string $prefix = '(', string $suffix = ')')
 * @method static Builder whereNotInAll(string $key, array $values, string $boolean = '&', string $operator = '!=', string $prefix = '[', string $suffix = ']')
 * @method static Builder orWhereNotInAll(string $key, array $values, string $boolean = '|', string $operator = '!=', string $prefix = '[', string $suffix = ']')
 * @method static Builder whereNotInExact(string $key, array $values, string $boolean = '&', string $operator = '!=', string $prefix = '{', string $suffix = '}')
 * @method static Builder orWhereNotInExact(string $key, array $values, string $boolean = '|', string $operator = '!=', string $prefix = '{', string $suffix = '}')
 * @method static Builder whereBetween(string $key, mixed $first, mixed $second, bool $withBoundaries = true, string $boolean = '&')
 * @method static Builder orWhereBetween(string $key, mixed $first, mixed $second, bool $withBoundaries = true, string $boolean = '|')
 * @method static Builder whereNotBetween(string $key, mixed $first, mixed $second, bool $withBoundaries = false, string $boolean = '&')
 * @method static Builder orWhereNotBetween(string $key, mixed $first, mixed $second, bool $withBoundaries = false, string $boolean = '|')
 * @method static Builder whereHas(string $key, string $boolean = '&')
 * @method static Builder orWhereHas(string $key, string $boolean = '|')
 * @method static Builder whereHasNot(string $key, string $boolean = '&')
 * @method static Builder orWhereHasNot(string $key, string $boolean = '|')
 * @method static Builder whereNull(string $key, string $boolean = '&')
 * @method static Builder orWhereNull(string $key, string $boolean = '|')
 * @method static Builder whereNotNull(string $key, string $boolean = '&')
 * @method static Builder orWhereNotNull(string $key, string $boolean = '|')
 * @method static Builder whereDate(string $key, mixed $operator, mixed|null $value = null, string $boolean = '&')
 * @method static Builder orWhereDate(string $key, mixed $operator, mixed|null $value = null, string $boolean = '|')
 * @method static Builder whereYear(string $key, mixed $operator, mixed|null $value = null, string $boolean = '&')
 * @method static Builder orWhereYear(string $key, mixed $operator, mixed|null $value = null, string $boolean = '|')
 * @method static Builder orderBy(string $key, string $direction = 'asc')
 * @method static Builder orderByDesc(string $key)
 * @method static Builder with(array $relationships)
 * @method static Builder cache(int $seconds)
 * @method static mixed|string get()
 * @method static mixed find(int $id)
 * @method static mixed findOrFail(int $id)
 * @method static mixed first()
 * @method static mixed firstOrFail()
 * @method static int|null count()
 * @method static \Illuminate\Support\Collection all()
 * @method static Paginator paginate(int $limit = 10)
 *
 * @package MarcReichel\IGDBLaravel\Models
 */
abstract class Model implements ModelInterface, ArrayAccess, Arrayable, Jsonable
{
    use HasAttributes, HasRelationships;

    private static Model $instance;

    public string|null $identifier;
    public Builder $builder;

    protected string $endpoint;

    /**
     * Model constructor.
     *
     * @param array $properties
     *
     * @throws ReflectionException
     */
    public function __construct(array $properties = [])
    {
        $this->builder = new Builder($this);
        self::$instance = $this;

        $this->setAttributes($properties);
        $this->setRelations($properties);
        $this->setIdentifier();
        $this->setEndpoint();
    }

    /**
     * @param string $field
     *
     * @return mixed
     */
    public function __get(string $field): mixed
    {
        return $this->getAttribute($field);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return void
     */
    public function __set(string $field, mixed $value): void
    {
        $this->attributes[$field] = $value;
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->attributes[$offset]) || isset($this->relations[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->getAttribute((string) $offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->attributes[$offset], $this->relations[$offset]);
    }

    /**
     * @param mixed $field
     *
     * @return bool
     */
    public function __isset(mixed $field): bool
    {
        return $this->offsetExists($field);
    }

    /**
     * @param mixed $field
     *
     * @return void
     */
    public function __unset(mixed $field): void
    {
        $this->offsetUnset($field);
    }

    /**
     * @param mixed $method
     * @param mixed $parameters
     *
     * @return mixed
     * @throws ReflectionException
     */
    public static function __callStatic(mixed $method, mixed $parameters)
    {
        $that = new static;
        return $that->forwardCallTo($that->newQuery(), $method, $parameters);
    }

    /**
     * @return void
     */
    protected function setIdentifier(): void
    {
        $this->identifier = collect($this->attributes)->get('id');
    }

    /**
     * @param array $attributes
     *
     * @return void
     */
    protected function setAttributes(array $attributes): void
    {
        $this->attributes = collect($attributes)->filter(function ($value) {
            if (is_array($value)) {
                return collect($value)->filter(function ($value) {
                    return is_object($value);
                })->isEmpty();
            }
            return !is_object($value);
        })->map(function ($value, $key) {
            $dates = collect($this->dates);
            if ($dates->contains($key)) {
                return Carbon::createFromTimestamp($value);
            }
            return $value;
        })->toArray();
    }

    /**
     * @param array $attributes
     *
     * @return void
     */
    protected function setRelations(array $attributes): void
    {
        $this->relations = collect($attributes)
            ->diffKeys($this->attributes)->map(function ($value, $key) {
                if (is_array($value)) {
                    return collect($value)->map(function ($value) use ($key) {
                        return $this->mapToModel($key, $value);
                    });
                }
                return $this->mapToModel($key, $value);
            });
    }

    /**
     * @param mixed $object
     * @param mixed $method
     * @param mixed $parameters
     *
     * @return mixed
     */
    public function forwardCallTo(mixed $object, mixed $method, mixed $parameters): mixed
    {
        try {
            return $object->$method(...$parameters);
        } catch (Error | BadMethodCallException $e) {
            throw new BadMethodCallException($e->getMessage());
        }
    }

    /**
     * @return Builder
     * @throws ReflectionException|InvalidParamsException
     */
    public function newQuery(): Builder
    {
        return new Builder($this);
    }

    /**
     * @param mixed $fields
     *
     * @return Model
     */
    public function getInstance(mixed $fields): Model
    {
        return self::$instance;
    }

    /**
     * @param string $field
     *
     * @return mixed
     */
    public function getAttribute(string $field): mixed
    {
        return collect($this->attributes)->merge($this->relations)->get($field);
    }

    /**
     * @param string $property
     * @param mixed $value
     *
     * @return mixed
     */
    private function mapToModel(string $property, mixed $value): mixed
    {
        $class = $this->getClassNameForProperty($property);

        if (!$class) {
            return $value;
        }

        if (is_object($value)) {
            $properties = $this->getProperties($value);

            return new $class($properties);
        }

        if (is_array($value)) {
            return collect($value)->map(function ($single) use ($class) {
                if (!is_object($single)) {
                    return $single;
                }

                $properties = $this->getProperties($single);

                return new $class($properties);
            })->toArray();
        }

        return [];
    }

    /**
     * @param string $property
     *
     * @return bool|null|string
     */
    protected function getClassNameForProperty(string $property): bool|null|string
    {
        if (collect($this->casts)->has($property)) {
            $class = collect($this->casts)->get($property);

            if (!is_null($class) && class_exists($class)) {
                return $class;
            }
        }

        $class = __NAMESPACE__ . '\\' . Str::singular(Str::studly($property));

        if (class_exists($class)) {
            return $class;
        }

        $class = __NAMESPACE__ . '\\' . class_basename(get_class($this)) . Str::singular(Str::studly($property));

        if (class_exists($class)) {
            return $class;
        }

        return false;
    }

    /**
     * @param mixed $value
     *
     * @return array
     */
    protected function getProperties(mixed $value): array
    {
        return collect($value)->toArray();
    }

    /**
     * @return void
     */
    protected function setEndpoint(): void
    {
        $class = class_basename(get_class($this));

        $this->endpoint = Str::snake(Str::plural($class));
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $attributes = collect($this->attributes);
        $relations = collect($this->relations)->map(function ($relation) {
            if (!$relation instanceof Arrayable) {
                return $relation;
            }

            return $relation->toArray();
        });
        return $attributes->merge($relations)->sortKeys()->toArray();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0): string
    {
        return collect($this->toArray())->toJson($options);
    }

    /**
     * @param string $method
     * @param array  $parameters
     *
     * @return Webhook
     * @throws AuthenticationException
     * @throws InvalidWebhookMethodException
     * @throws WebhookSecretMissingException
     * @throws RequestException
     * @throws Exception
     */
    public static function createWebhook(string $method, array $parameters = []): Webhook
    {
        if (!config('igdb.webhook_secret')) {
            throw new WebhookSecretMissingException();
        }

        $self = (new static);

        $routeParameters = array_merge($parameters, [
            'model' => $self->getEndpoint(),
            'method' => $method,
        ]);

        $url = route('handle-igdb-webhook', $routeParameters);

        $reflectionClass = new ReflectionClass(Method::class);
        $allowedMethods = array_values($reflectionClass->getConstants());

        if (!in_array($method, $allowedMethods, true)) {
            throw new InvalidWebhookMethodException();
        }

        $endpoint = $self->endpoint . '/webhooks';

        $client = Http::withOptions([
            'base_uri' => ApiHelper::IGDB_BASE_URI,
        ])->withHeaders([
            'Accept' => 'application/json',
            'Client-ID' => config('igdb.credentials.client_id'),
            'Authorization' => 'Bearer ' . ApiHelper::retrieveAccessToken(),
        ])
        ->asForm();

        $response = $client->post($endpoint, [
            'url' => $url,
            'method' => $method,
            'secret' => config('igdb.webhook_secret'),
        ])->throw()->json();

        if (!is_array($response)) {
            throw new Exception('An error occured while trying to create the webhook.');
        }

        return new Webhook(...$response);
    }
}
