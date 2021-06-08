<?php

namespace MarcReichel\IGDBLaravel\Models;

use Error;
use ArrayAccess;
use Carbon\Carbon;
use BadMethodCallException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use MarcReichel\IGDBLaravel\ApiHelper;
use MarcReichel\IGDBLaravel\Builder;
use Illuminate\Contracts\Support\{Jsonable, Arrayable};
use MarcReichel\IGDBLaravel\Traits\{HasAttributes, HasRelationships};
use MarcReichel\IGDBLaravel\Enums\Webhook\Method;
use MarcReichel\IGDBLaravel\Exceptions\AuthenticationException;
use MarcReichel\IGDBLaravel\Exceptions\InvalidWebhookMethodException;
use MarcReichel\IGDBLaravel\Exceptions\InvalidWebhookUrlException;
use MarcReichel\IGDBLaravel\Exceptions\WebhookSecretMissingException;
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
abstract class Model implements ArrayAccess, Arrayable, Jsonable
{
    use HasAttributes, HasRelationships;

    private static $instance;

    public $identifier;
    public $builder;

    protected $endpoint;

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
     * @param $field
     *
     * @return mixed
     */
    public function __get($field)
    {
        return $this->getAttribute($field);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return void
     */
    public function __set(string $field, $value): void
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
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
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
    public function __isset($field): bool
    {
        return $this->offsetExists($field);
    }

    /**
     * @param mixed $field
     *
     * @return void
     */
    public function __unset($field): void
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
    public static function __callStatic($method, $parameters)
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
    public function forwardCallTo($object, $method, $parameters)
    {
        try {
            return $object->$method(...$parameters);
        } catch (Error | BadMethodCallException $e) {
            throw new BadMethodCallException($e->getMessage());
        }
    }

    /**
     * @return Builder
     * @throws ReflectionException
     */
    public function newQuery(): Builder
    {
        return new Builder($this);
    }

    /**
     * @param mixed $fields
     *
     * @return Model
     * @throws ReflectionException
     */
    public function getInstance($fields): Model
    {
        if (is_null(self::$instance)) {
            $model = new static($fields);
            self::$instance = $model;
        }

        return self::$instance;
    }

    /**
     * @param mixed $field
     *
     * @return mixed
     */
    public function getAttribute($field)
    {
        return collect($this->attributes)->merge($this->relations)->get($field);
    }

    /**
     * @param mixed $property
     * @param mixed $value
     *
     * @return mixed
     */
    private function mapToModel($property, $value)
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
     * @param mixed $property
     *
     * @return bool|mixed|string
     */
    protected function getClassNameForProperty($property)
    {
        if (collect($this->casts)->has($property)) {
            $class = collect($this->casts)->get($property);

            if (class_exists($class)) {
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
    protected function getProperties($value): array
    {
        return collect($value)->toArray();
    }

    /**
     * @return void
     */
    protected function setEndpoint(): void
    {
        if (!$this->endpoint) {
            $class = class_basename(get_class($this));

            $this->endpoint = Str::snake(Str::plural($class));
        }
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
     * @param string $url
     * @param string $method
     *
     * @return Webhook
     * @throws AuthenticationException
     * @throws WebhookSecretMissingException|InvalidWebhookMethodException|InvalidWebhookUrlException
     */
    public static function createWebhook(string $url, string $method): Webhook
    {
        if (!config('igdb.webhook_secret')) {
            throw new WebhookSecretMissingException();
        }

        $parsedUrl = parse_url($url);

        if (!$parsedUrl) {
            throw new InvalidWebhookUrlException($url);
        }

        $reflectionClass = new ReflectionClass(Method::class);
        $allowedMethods = array_values($reflectionClass->getConstants());

        if (!in_array($method, $allowedMethods, true)) {
            throw new InvalidWebhookMethodException();
        }

        $self = (new static);

        $endpoint = $self->endpoint . '/webhooks';

        $client = Http::withOptions([
            'base_uri' => ApiHelper::IGDB_BASE_URI,
        ])->withHeaders([
            'Accept' => 'application/json',
            'Client-ID' => config('igdb.credentials.client_id'),
            'Authorization' => 'Bearer ' . ApiHelper::retrieveAccessToken(),
        ])
        ->asForm();

        parse_str($parsedUrl['query'] ?? '', $queryParams);

        $collection = collect($queryParams);

        $collection->put('x_igdb_endpoint', $self->getEndpoint());
        $collection->put('x_igdb_method', $method);

        $modifiedQueryString = http_build_query($collection->toArray());
        $newUrl = ($parsedUrl['scheme'] ?? 'http') . '://' . $parsedUrl['host'] . $parsedUrl['path']
            . ($modifiedQueryString ? '?' . $modifiedQueryString : '');

        return new Webhook(...$client->post($endpoint, [
            'url' => $newUrl,
            'method' => $method,
            'secret' => config('igdb.webhook_secret'),
        ])->json());
    }
}
