<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

use ArrayAccess;
use BadMethodCallException;
use Carbon\Carbon;
use Error;
use Exception;
use Illuminate\Contracts\Support\{Arrayable, Jsonable};
use Illuminate\Http\Client\RequestException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use MarcReichel\IGDBLaravel\ApiHelper;
use MarcReichel\IGDBLaravel\Builder;
use MarcReichel\IGDBLaravel\Enums\Webhook\Method;
use MarcReichel\IGDBLaravel\Exceptions\AuthenticationException;
use MarcReichel\IGDBLaravel\Exceptions\InvalidParamsException;
use MarcReichel\IGDBLaravel\Exceptions\InvalidWebhookMethodException;
use MarcReichel\IGDBLaravel\Exceptions\WebhookSecretMissingException;
use MarcReichel\IGDBLaravel\Interfaces\ModelInterface;
use MarcReichel\IGDBLaravel\Traits\{HasAttributes, HasRelationships};
use ReflectionException;

/**
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
 */
abstract class Model implements Arrayable, ArrayAccess, Jsonable, ModelInterface
{
    use HasAttributes;
    use HasRelationships;

    public ?string $identifier;
    public Builder $builder;

    protected string $endpoint;

    /**
     * @throws ReflectionException
     * @throws InvalidParamsException
     */
    public function __construct(array $properties = [])
    {
        $this->builder = new Builder($this);

        $this->setAttributes($properties);
        $this->setRelations($properties);
        $this->setIdentifier();
        $this->setEndpoint();
    }

    public function __get(string $field): mixed
    {
        return $this->getAttribute($field);
    }

    public function __set(string $field, mixed $value): void
    {
        $this->attributes[$field] = $value;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->attributes[$offset]) || isset($this->relations[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->getAttribute((string) $offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->attributes[$offset], $this->relations[$offset]);
    }

    public function __isset(mixed $field): bool
    {
        return $this->offsetExists($field);
    }

    public function __unset(mixed $field): void
    {
        $this->offsetUnset($field);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidParamsException
     */
    public static function __callStatic(mixed $method, mixed $parameters): mixed
    {
        $that = new static();

        return $that->forwardCallTo($that->newQuery(), $method, $parameters);
    }

    protected function setIdentifier(): void
    {
        $this->identifier = (string) collect($this->attributes)->get('id');
    }

    protected function setAttributes(array $attributes): void
    {
        $this->attributes = collect($attributes)->filter(function ($value) {
            if (is_array($value)) {
                return collect($value)->filter(fn ($value) => is_object($value))->isEmpty();
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

    protected function setRelations(array $attributes): void
    {
        $this->relations = collect($attributes)
            ->filter(fn ($value, $key) => array_key_exists($key, $this->casts))
            ->map(function ($value, $key) {
                if (is_array($value) && array_is_list($value)) {
                    return collect($value)->map(fn ($value) => $this->mapToModel($key, $value))->filter();
                }

                return $this->mapToModel($key, $value);
            })
            ->filter(fn (mixed $value): bool => $value instanceof Model || ($value instanceof \Illuminate\Support\Collection && !$value->isEmpty()));
    }

    public function forwardCallTo(mixed $object, mixed $method, mixed $parameters): mixed
    {
        try {
            return $object->$method(...$parameters);
        } catch (BadMethodCallException | Error $e) {
            throw new BadMethodCallException($e->getMessage());
        }
    }

    /**
     * @throws ReflectionException
     * @throws InvalidParamsException
     */
    public function newQuery(): Builder
    {
        return new Builder($this);
    }

    public function getAttribute(string $field): mixed
    {
        return collect($this->attributes)->merge($this->relations)->get($field);
    }

    private function mapToModel(string $property, mixed $value): mixed
    {
        $class = $this->getClassNameForProperty($property);

        if (!$class) {
            return $value;
        }

        if (is_array($value) && !array_is_list($value)) {
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

        return null;
    }

    protected function getClassNameForProperty(string $property): bool | string | null
    {
        if (collect($this->casts)->has($property)) {
            $class = collect($this->casts)->get($property);

            if (null !== $class && class_exists($class)) {
                return $class;
            }
        }

        $class = __NAMESPACE__ . '\\' . Str::singular(Str::studly($property));

        if (class_exists($class)) {
            return $class;
        }

        $class = __NAMESPACE__ . '\\' . class_basename(static::class) . Str::singular(Str::studly($property));

        if (class_exists($class)) {
            return $class;
        }

        return false;
    }

    protected function getProperties(mixed $value): array
    {
        return collect($value)->toArray();
    }

    protected function setEndpoint(): void
    {
        $class = class_basename(static::class);

        $this->endpoint = Str::snake(Str::plural($class));
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Get the instance as an array.
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
     */
    public function toJson($options = 0): string
    {
        return collect($this->toArray())->toJson($options);
    }

    /**
     * @throws AuthenticationException
     * @throws WebhookSecretMissingException
     * @throws RequestException
     * @throws Exception
     */
    public static function createWebhook(Method | string $method, array $parameters = []): Webhook
    {
        if (!config('igdb.webhook_secret')) {
            throw new WebhookSecretMissingException();
        }

        $self = new static();

        if ($method instanceof Method) {
            $parsedMethod = $method->value;
        } else {
            $parsedMethod = $method;
        }

        if (!in_array($parsedMethod, ['create', 'update', 'delete'])) {
            throw new InvalidWebhookMethodException();
        }

        $routeParameters = array_merge($parameters, [
            'model' => $self->getEndpoint(),
            'method' => $parsedMethod,
        ]);

        $url = route('handle-igdb-webhook', $routeParameters);

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
            'method' => $parsedMethod,
            'secret' => config('igdb.webhook_secret'),
        ])->throw()->json();

        if (!is_array($response)) {
            throw new Exception('An error occured while trying to create the webhook.');
        }

        return new Webhook(...$response);
    }
}
