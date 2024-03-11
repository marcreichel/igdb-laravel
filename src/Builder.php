<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use JsonException;
use MarcReichel\IGDBLaravel\Exceptions\InvalidParamsException;
use MarcReichel\IGDBLaravel\Exceptions\MissingEndpointException;
use MarcReichel\IGDBLaravel\Exceptions\ModelNotFoundException;
use MarcReichel\IGDBLaravel\Traits\{DateCasts,
    HasLimits,
    HasNestedWhere,
    HasSearch,
    HasSelect,
    HasWhere,
    HasWhereBetween,
    HasWhereDate,
    HasWhereHas,
    HasWhereIn,
    HasWhereLike,
    Operators,
    ValuePreparer};
use ReflectionClass;
use ReflectionException;
use stdClass;

class Builder
{
    use DateCasts;
    use Operators;
    use HasSelect;
    use HasLimits;
    use HasSearch;
    use ValuePreparer;
    use HasWhere;
    use HasWhereLike;
    use HasNestedWhere;
    use HasWhereIn;
    use HasWhereBetween;
    use HasWhereHas;
    use HasWhereDate;

    /**
     * The endpoint of the API that should be requested.
     */
    private string $endpoint;

    /**
     * The Class the request results should be mapped to.
     */
    private mixed $class;

    /**
     * The query data that should be attached to the request.
     */
    private Collection $query;

    /**
     * The cache lifetime.
     */
    private int $cacheLifetime;

    /**
     * @throws ReflectionException|InvalidParamsException
     */
    public function __construct(mixed $model = null)
    {
        if ($model) {
            $this->setEndpoint($model);
        }

        $this->init();
    }

    protected function init(): void
    {
        $this->query = new Collection(['fields' => new Collection(['*'])]);
        $cache = config('igdb.cache_lifetime');
        if (!is_int($cache)) {
            throw new InvalidArgumentException('igdb.cache_lifetime needs to be int. ' . gettype($cache) . ' given.');
        }

        $this->cacheLifetime = $cache;
    }

    /**
     * Add a "sort" clause to the query.
     *
     * @throws InvalidParamsException
     */
    public function orderBy(string $key, string $direction = 'asc'): self
    {
        if (!in_array($direction, ['asc', 'desc'])) {
            $message = 'Expected `asc` or `desc` as order direction. `' . $direction . '` given.';

            throw new InvalidParamsException($message);
        }

        if (!$this->query->has('search')) {
            $this->query->put('sort', $key . ' ' . $direction);
        }

        return $this;
    }

    /**
     * Add a "sort desc" clause to the query.
     *
     * @throws InvalidParamsException
     */
    public function orderByDesc(string $key): self
    {
        return $this->orderBy($key, 'desc');
    }

    /**
     * Add an "expand" clause to the query.
     */
    public function with(array $relationships): self
    {
        $relationships = collect($relationships)->mapWithKeys(function (
            mixed $fields,
            mixed $relationship,
        ) {
            if (is_numeric($relationship)) {
                return [$fields => ['*']];
            }

            return [$relationship => $fields];
        })->map(function (mixed $fields, mixed $relationship) {
            if (collect($fields)->count() === 0) {
                $fields = ['*'];
            }

            return collect($fields)->map(fn (mixed $field) => $relationship . '.' . $field)->implode(',');
        })
            ->values()
            ->toArray();

        $select = $this->query->get('fields', new Collection());

        $fields = collect($relationships)->flatten()->filter()->toArray();

        $this->query->put('fields', $select->push($fields)->flatten());

        return $this;
    }

    /**
     * Overwrite the cache lifetime for this query.
     */
    public function cache(int $seconds): self
    {
        $this->cacheLifetime = $seconds;

        return $this;
    }

    /**
     * Get the resulting query.
     */
    public function getQuery(): string
    {
        return $this->query->map(function (mixed $value, string $key) {
            if ($key === 'where') {
                return collect($value)->unique()->implode(' ');
            }
            if ($key === 'fields') {
                return collect($value)->unique()->sortBy(fn (mixed $field) => count(explode('.', $field)))->implode(',');
            }

            return $value;
        })->map(fn (mixed $value, string $key) => Str::finish($key . ' ' . $value, ';'))->unique()->sort()->implode("\n");
    }

    /**
     * Set the endpoint as string.
     *
     * @deprecated Will be removed in the next major release.
     */
    public function endpoint(string $endpoint): self
    {
        if (!isset($this->class)) {
            $this->endpoint = $endpoint;
        }

        return $this;
    }

    /**
     * Set the endpoint from model or string.
     *
     * @throws ReflectionException
     * @throws InvalidParamsException
     */
    protected function setEndpoint(mixed $model): void
    {
        $neededNamespace = __NAMESPACE__ . '\\Models';

        if (is_object($model)) {
            $class = $model::class;
            $classParents = class_parents($model);
            $parents = $classParents ? collect($classParents) : collect();

            if ($parents->isEmpty()) {
                $parents->push($class);
            }

            $reflectionClass = new ReflectionClass($parents->last() ?? new stdClass());
            $reflectionNamespace = $reflectionClass->getNamespaceName();

            if (Str::startsWith($reflectionNamespace, $neededNamespace)) {
                $this->class = $model::class;
                $class = class_basename($this->class);
                $this->endpoint = Str::snake(Str::plural($class));
            }
        } elseif (is_string($model)) {
            $this->endpoint = $model;
        }

        if (!isset($this->endpoint)) {
            $message = 'Construction-Parameter of Builder must be a string or a Class which extends ' . $neededNamespace . '\\Model. ' . ucfirst(gettype($model)) . ' given.';

            throw new InvalidArgumentException($message);
        }
    }

    /**
     * Cast a value as date.
     */
    protected function castDate(mixed $date): mixed
    {
        if (is_string($date)) {
            return Carbon::parse($date)->timestamp;
        }

        if ($date instanceof Carbon) {
            return $date->timestamp;
        }

        return $date;
    }

    /**
     * Execute the query.
     *
     * @throws MissingEndpointException
     */
    public function get(): mixed
    {
        if (!isset($this->endpoint) || $this->endpoint === '') {
            throw new MissingEndpointException();
        }

        $data = Client::get($this->endpoint, $this->getQuery(), $this->cacheLifetime);

        if (isset($this->class) && $this->class) {
            $data = collect($data)->map(fn (mixed $result) => $this->mapToModel($result));
        }

        $this->init();

        return $data;
    }

    private function mapToModel(mixed $result): mixed
    {
        $model = $this->class;

        $properties = collect($result)->toArray();
        $model = new $model($properties);

        unset($model->builder);

        return $model;
    }

    /**
     * Execute a query for a single record by ID.
     *
     * @throws MissingEndpointException
     * @throws ReflectionException
     * @throws JsonException
     * @throws InvalidParamsException
     */
    public function find(int $id): mixed
    {
        return $this->where('id', $id)->first();
    }

    /**
     * @throws MissingEndpointException
     * @throws ModelNotFoundException
     * @throws ReflectionException
     * @throws JsonException
     * @throws InvalidParamsException
     */
    public function findOrFail(int $id): mixed
    {
        $data = $this->find($id);

        if ($data) {
            return $data;
        }

        if (isset($this->class) && is_string($this->class)) {
            $model = class_basename($this->class);
        } else {
            $model = Str::studly(Str::singular($this->endpoint));
        }

        $message = sprintf('%s with id %d not found.', $model, $id);

        throw new ModelNotFoundException($message);
    }

    /**
     * Execute the query and get the first result.
     *
     * @throws MissingEndpointException
     */
    public function first(): mixed
    {
        $data = $this->skip(0)->take(1)->get();

        return collect($data)->first();
    }

    /**
     * @throws MissingEndpointException
     * @throws ModelNotFoundException
     */
    public function firstOrFail(): mixed
    {
        $data = $this->first();

        if ($data) {
            return $data;
        }

        if (isset($this->class) && is_string($this->class)) {
            $model = Str::plural(class_basename($this->class));
        } else {
            $model = Str::studly(Str::plural($this->endpoint));
        }

        $message = sprintf('No %s found.', $model);

        throw new ModelNotFoundException($message);
    }

    /**
     * Return the total "count" result of the query.
     *
     * @throws MissingEndpointException
     */
    public function count(): int
    {
        if (!isset($this->endpoint) || $this->endpoint === '') {
            throw new MissingEndpointException();
        }

        $data = Client::count($this->endpoint, $this->getQuery(), $this->cacheLifetime);

        $this->init();

        return $data;
    }

    /**
     * @throws MissingEndpointException
     */
    public function all(): Collection
    {
        $data = $this->skip(0)->take(500)->get();

        return collect($data);
    }

    /**
     * @throws MissingEndpointException
     */
    public function paginate(int $limit = 10): Paginator
    {
        return new Paginator(
            $this->forPage((int) (request()->query('page', '1') ?? 1), $limit)->get(),
            $limit,
        );
    }
}
