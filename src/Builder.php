<?php

namespace MarcReichel\IGDBLaravel;

use Carbon\Carbon;
use Closure;
use DateTimeInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use InvalidArgumentException;
use JsonException;
use MarcReichel\IGDBLaravel\Exceptions\InvalidParamsException;
use MarcReichel\IGDBLaravel\Exceptions\MissingEndpointException;
use MarcReichel\IGDBLaravel\Exceptions\ModelNotFoundException;
use MarcReichel\IGDBLaravel\Traits\{DateCasts, Operators};
use ReflectionClass;
use ReflectionException;

class Builder
{
    use Operators, DateCasts;

    /**
     * The HTTP Client to request data from the API.
     *
     * @var PendingRequest
     */
    private PendingRequest $client;

    /**
     * The endpoint of the API that should be requested.
     *
     * @var string
     */
    private string $endpoint;

    /**
     * The Class the request results should be mapped to.
     *
     * @var mixed
     */
    private mixed $class;

    /**
     * The query data that should be attached to the request.
     *
     * @var Collection
     */
    private Collection $query;

    /**
     * The cache lifetime.
     *
     * @var int
     */
    private int $cacheLifetime;

    /**
     * Builder constructor.
     *
     * @param mixed $model
     *
     * @throws ReflectionException|InvalidParamsException
     */
    public function __construct(mixed $model = null)
    {
        if ($model) {
            $this->setEndpoint($model);
        }

        $this->init();
    }

    /**
     * Set the fields to be selected.
     *
     * @param mixed $fields
     *
     * @return self
     */
    public function select(mixed $fields): self
    {
        $fields = is_array($fields) ? $fields : func_get_args();
        $collection = collect($fields);

        if ($collection->isEmpty()) {
            $collection->push('*');
        }

        $collection = $collection->filter(function ($field) {
            return !strpos($field, '.');
        })->flatten();

        if ($collection->isEmpty()) {
            $collection->push('*');
        }

        $this->query->put('fields', $collection);

        return $this;
    }

    /**
     * @return void
     */
    protected function init(): void
    {
        $this->initClient();
        $this->initQuery();
        $this->resetCacheLifetime();
    }

    /**
     * @return void
     */
    private function initClient(): void
    {
        $this->client = Http::withOptions([
            'base_uri' => ApiHelper::IGDB_BASE_URI,
        ])->withHeaders([
            'Accept' => 'application/json',
            'Client-ID' => config('igdb.credentials.client_id'),
        ]);
    }

    /**
     * Init the default query.
     *
     * @return void
     */
    protected function initQuery(): void
    {
        $this->query = new Collection(['fields' => new Collection(['*'])]);
    }

    /**
     * Reset the cache lifetime.
     *
     * @return void
     */
    protected function resetCacheLifetime(): void
    {
        $cache = config('igdb.cache_lifetime');
        if (!is_int($cache)) {
            throw new InvalidArgumentException('igdb.cache_lifetime needs to be int. ' . gettype($cache) . ' given.');
        }

        $this->cacheLifetime = $cache;
    }

    /**
     * Set the "limit" value of the query.
     *
     * @param int $limit
     *
     * @return self
     */
    public function limit(int $limit): self
    {
        $limit = min($limit, 500);
        $this->query->put('limit', $limit);

        return $this;
    }

    /**
     * Alias to set the "limit" value of the query.
     *
     * @param int $limit
     *
     * @return self
     */
    public function take(int $limit): self
    {
        return $this->limit($limit);
    }

    /**
     * Set the "offset" value of the query.
     *
     * @param int $offset
     *
     * @return self
     */
    public function offset(int $offset): self
    {
        $this->query->put('offset', $offset);

        return $this;
    }

    /**
     * Alias to set the "offset" value of the query.
     *
     * @param int $offset
     *
     * @return self
     */
    public function skip(int $offset): self
    {
        return $this->offset($offset);
    }

    /**
     * Set the limit and offset for a given page.
     *
     * @param int $page
     * @param int $perPage
     *
     * @return self
     */
    public function forPage(int $page, int $perPage = 10): self
    {
        return $this->skip(($page - 1) * $perPage)->take($perPage);
    }

    /**
     * Search for the given string.
     *
     * @param string $query
     *
     * @return self
     */
    public function search(string $query): self
    {
        $this->query->put('search', '"' . $query . '"');

        return $this;
    }

    /**
     * Add a fuzzy search to the query.
     *
     * @param mixed  $key
     * @param string $query
     * @param bool   $caseSensitive
     * @param string $boolean
     *
     * @return self
     * @throws ReflectionException|InvalidParamsException
     */
    public function fuzzySearch(
        mixed $key,
        string $query,
        bool $caseSensitive = false,
        string $boolean = '&',
    ): self {
        $tokenizedQuery = explode(' ', $query);
        $keys = collect($key)->crossJoin($tokenizedQuery)->toArray();

        return $this->whereNested(function ($query) use ($keys, $caseSensitive) {
            foreach ($keys as $v) {
                if (is_array($v)) {
                    $query->whereLike($v[0], $v[1], $caseSensitive, '|');
                }
            }
        }, $boolean);
    }

    /**
     * Add an "or fuzzy search" to the query.
     *
     * @param mixed  $key
     * @param string $query
     * @param bool   $caseSensitive
     * @param string $boolean
     *
     * @return self
     * @throws ReflectionException|InvalidParamsException
     */
    public function orFuzzySearch(
        mixed $key,
        string $query,
        bool $caseSensitive = false,
        string $boolean = '|',
    ): self {
        return $this->fuzzySearch($key, $query, $caseSensitive, $boolean);
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param mixed      $key
     * @param mixed|null $operator
     * @param mixed|null $value
     * @param string     $boolean
     *
     * @return self
     * @throws ReflectionException|JsonException|InvalidParamsException
     */
    public function where(
        mixed $key,
        mixed $operator = null,
        mixed $value = null,
        string $boolean = '&'
    ): self {
        if ($key instanceof Closure) {
            return $this->whereNested($key, $boolean);
        }

        if (is_array($key)) {
            return $this->addArrayOfWheres($key, $boolean);
        }

        if (!is_string($key)) {
            throw new InvalidArgumentException('Parameter #1 $key needs to be string. ' . gettype($key) . ' given.');
        }

        [$value, $operator] = $this->prepareValueAndOperator($value, $operator,
            func_num_args() === 2);

        $select = $this->query->get('fields', new Collection());
        if (!$select->contains($key) && !$select->contains('*')) {
            $this->query->put('fields', $select->push($key));
        }

        $where = $this->query->get('where', new Collection());

        if (collect($this->dates)->has($key) && $this->dates[$key] === 'date') {
            $value = $this->castDate($value);
        }

        if (is_string($value)) {
            if ($operator === 'like') {
                $this->whereLike($key, $value, true, $boolean);
            } elseif ($operator === 'ilike') {
                $this->whereLike($key, $value, false, $boolean);
            } elseif ($operator === 'not like') {
                $this->whereNotLike($key, $value, true, $boolean);
            } elseif ($operator === 'not ilike') {
                $this->whereNotLike($key, $value, false, $boolean);
            } else {
                $where->push(($where->count() ? $boolean . ' ' : '') . $key . ' ' . $operator . ' "' . $value . '"');
                $this->query->put('where', $where);
            }
        } else {
            $value = !is_int($value) ? json_encode($value, JSON_THROW_ON_ERROR) : $value;
            $where->push(($where->count() ? $boolean . ' ' : '') . $key . ' ' . $operator . ' ' . $value);
            $this->query->put('where', $where);
        }

        return $this;
    }

    /**
     * Add an "or where" clause to the query.
     *
     * @param mixed       $key
     * @param string|null $operator
     * @param mixed|null  $value
     * @param string      $boolean
     *
     * @return self
     * @throws ReflectionException|JsonException|InvalidParamsException
     */
    public function orWhere(
        mixed $key,
        string $operator = null,
        mixed $value = null,
        string $boolean = '|'
    ): self {
        if ($key instanceof Closure) {
            return $this->whereNested($key, $boolean);
        }

        if (is_array($key)) {
            return $this->addArrayOfWheres($key, $boolean);
        }

        [$value, $operator] = $this->prepareValueAndOperator($value, $operator,
            func_num_args() === 2);

        return $this->where($key, $operator, $value, $boolean);
    }

    /**
     * Add a "where like" clause to the query.
     *
     * @param string $key
     * @param string $value
     * @param bool   $caseSensitive
     * @param string $boolean
     *
     * @return self
     * @throws JsonException
     */
    public function whereLike(
        string $key,
        string $value,
        bool $caseSensitive = true,
        string $boolean = '&'
    ): self {
        $where = $this->query->get('where', new Collection());

        $clause = $this->generateWhereLikeClause($key, $value, $caseSensitive, '=', '~');

        $where->push(($where->count() ? $boolean . ' ' : '') . $clause);

        $this->query->put('where', $where);

        return $this;
    }

    /**
     * Add an "or where like" clause to the query.
     *
     * @param string $key
     * @param string $value
     * @param bool   $caseSensitive
     * @param string $boolean
     *
     * @return self
     * @throws JsonException
     */
    public function orWhereLike(
        string $key,
        string $value,
        bool $caseSensitive = true,
        string $boolean = '|'
    ): self {
        return $this->whereLike($key, $value, $caseSensitive, $boolean);
    }

    /**
     * Add a "where not like" clause to the query.
     *
     * @param string $key
     * @param string $value
     * @param bool   $caseSensitive
     * @param string $boolean
     *
     * @return self
     * @throws JsonException
     */
    public function whereNotLike(
        string $key,
        string $value,
        bool $caseSensitive = true,
        string $boolean = '&'
    ): self {
        $where = $this->query->get('where', new Collection());

        $clause = $this->generateWhereLikeClause($key, $value, $caseSensitive, '!=', '!~');

        $where->push(($where->count() ? $boolean . ' ' : '') . $clause);

        $this->query->put('where', $where);

        return $this;
    }

    /**
     * Add an "or where not like" clause to the query.
     *
     * @param string $key
     * @param string $value
     * @param bool   $caseSensitive
     * @param string $boolean
     *
     * @return self
     * @throws JsonException
     */
    public function orWhereNotLike(
        string $key,
        string $value,
        bool $caseSensitive = true,
        string $boolean = '|'
    ): self {
        return $this->whereNotLike($key, $value, $caseSensitive, $boolean);
    }

    /**
     * @param string $key
     * @param string $value
     * @param bool   $caseSensitive
     * @param string $operator
     * @param string $insensitiveOperator
     *
     * @return string
     * @throws JsonException
     */
    private function generateWhereLikeClause(
        string $key,
        string $value,
        bool $caseSensitive,
        string $operator,
        string $insensitiveOperator
    ): string {
        $hasPrefix = Str::startsWith($value, ['%', '*']);
        $hasSuffix = Str::endsWith($value, ['%', '*']);

        if ($hasPrefix) {
            $value = substr($value, 1);
        }
        if ($hasSuffix) {
            $value = substr($value, 0, -1);
        }

        $operator = $caseSensitive ? $operator : $insensitiveOperator;
        $prefix = $hasPrefix || !$hasSuffix ? '*' : '';
        $suffix = $hasSuffix || !$hasPrefix ? '*' : '';
        $value = json_encode($value, JSON_THROW_ON_ERROR);
        $value = Str::start(Str::finish($value, $suffix), $prefix);

        return implode(' ', [$key, $operator, $value]);
    }

    /**
     * Prepare the value and operator for a where clause.
     *
     * @param mixed $value
     * @param mixed $operator
     * @param bool  $useDefault
     *
     * @return array
     */
    private function prepareValueAndOperator(
        mixed $value,
        mixed $operator,
        bool $useDefault = false
    ): array {
        if ($useDefault) {
            return [$operator, '='];
        }

        if (!is_string($operator) && !is_null($operator)) {
            throw new InvalidArgumentException('Parameter #2 $operator needs to be string or null. ' . gettype($operator) . ' given.');
        }

        if ($this->invalidOperatorAndValue($operator, $value)) {
            throw new InvalidArgumentException('Illegal operator and value combination.');
        }

        return [$value, $operator];
    }

    /**
     * Determine if the given operator and value combination is legal.
     *
     * Prevents using Null values with invalid operators.
     *
     * @param string|null $operator
     * @param mixed       $value
     *
     * @return bool
     */
    private function invalidOperatorAndValue(string|null $operator, mixed $value): bool
    {
        return is_null($value) && in_array($operator, $this->operators, true) && !in_array($operator, ['=', '!=']);
    }

    /**
     * Add an array of where clauses to the query.
     *
     * @param array  $arrayOfWheres
     * @param string $boolean
     * @param string $method
     *
     * @return self
     * @throws ReflectionException|InvalidParamsException
     */
    protected function addArrayOfWheres(
        array $arrayOfWheres,
        string $boolean,
        string $method = 'where'
    ): self {
        return $this->whereNested(function ($query) use (
            $arrayOfWheres,
            $method,
            $boolean
        ) {
            foreach ($arrayOfWheres as $key => $value) {
                if (is_numeric($key) && is_array($value)) {
                    $query->$method(...array_values($value));
                } else {
                    $query->$method($key, '=', $value, $boolean);
                }
            }
        }, $boolean);
    }

    /**
     * Add a nested where statement to the query.
     *
     * @param Closure $callback
     * @param string  $boolean
     *
     * @return self
     * @throws ReflectionException|InvalidParamsException
     */
    protected function whereNested(Closure $callback, string $boolean = '&'): self
    {
        if (isset($this->class) && $this->class) {
            $class = $this->class;
            $callback($query = new Builder(new $class()));
        } else {
            $callback($query = new Builder($this->endpoint));
        }

        return $this->addNestedWhereQuery($query, $boolean);
    }

    /**
     * Add another query builder as a nested where to the query builder.
     *
     * @param Builder $query
     * @param string  $boolean
     *
     * @return self
     */
    protected function addNestedWhereQuery(Builder $query, string $boolean): self
    {
        $where = $this->query->get('where', new Collection());

        $nested = '(' . $query->query->get('where')->implode(' ') . ')';

        $where->push(($where->count() ? $boolean . ' ' : '') . $nested);

        $this->query->put('where', $where);

        return $this;
    }

    /**
     * Add a "where in" clause to the query.
     *
     * @param string $key
     * @param array  $values
     * @param string $boolean
     * @param string $operator
     * @param string $prefix
     * @param string $suffix
     *
     * @return self
     */
    public function whereIn(
        string $key,
        array $values,
        string $boolean = '&',
        string $operator = '=',
        string $prefix = '(',
        string $suffix = ')'
    ): self {
        if (($prefix === '(' && $suffix !== ')') || ($prefix === '[' && $suffix !== ']') || ($prefix === '{' && $suffix !== '}')) {
            $message = 'Prefix and Suffix must be "(" and ")", "[" and "]" or "{" and "}".';
            throw new InvalidArgumentException($message);
        }

        $where = $this->query->get('where', new Collection());

        $valuesString = collect($values)->map(function ($value) {
            return !is_numeric($value) ? '"' . $value . '"' : $value;
        })->implode(',');

        $where->push(($where->count() ? $boolean . ' ' : '') . $key . ' ' . $operator . ' ' . $prefix . $valuesString . $suffix);

        $this->query->put('where', $where);

        return $this;
    }

    /**
     * Add an "or where in" clause to the query.
     *
     * @param string $key
     * @param array  $value
     * @param string $boolean
     * @param string $operator
     * @param string $prefix
     * @param string $suffix
     *
     * @return self
     */
    public function orWhereIn(
        string $key,
        array $value,
        string $boolean = '|',
        string $operator = '=',
        string $prefix = '(',
        string $suffix = ')'
    ): self {
        return $this->whereIn($key, $value, $boolean, $operator, $prefix,
            $suffix);
    }

    /**
     * Add a "where in all" clause to the query.
     *
     * @param string $key
     * @param array  $values
     * @param string $boolean
     * @param string $operator
     * @param string $prefix
     * @param string $suffix
     *
     * @return self
     */
    public function whereInAll(
        string $key,
        array $values,
        string $boolean = '&',
        string $operator = '=',
        string $prefix = '[',
        string $suffix = ']'
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix,
            $suffix);
    }

    /**
     * Add an "or where in all" clause to the query.
     *
     * @param string $key
     * @param array  $values
     * @param string $boolean
     * @param string $operator
     * @param string $prefix
     * @param string $suffix
     *
     * @return self
     */
    public function orWhereInAll(
        string $key,
        array $values,
        string $boolean = '|',
        string $operator = '=',
        string $prefix = '[',
        string $suffix = ']',
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix,
            $suffix);
    }

    /**
     * Add a "where in exact" clause to the query.
     *
     * @param string $key
     * @param array  $values
     * @param string $boolean
     * @param string $operator
     * @param string $prefix
     * @param string $suffix
     *
     * @return self
     */
    public function whereInExact(
        string $key,
        array $values,
        string $boolean = '&',
        string $operator = '=',
        string $prefix = '{',
        string $suffix = '}',
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix,
            $suffix);
    }

    /**
     * Add an "or where in exact" clause to the query.
     *
     * @param string $key
     * @param array  $values
     * @param string $boolean
     * @param string $operator
     * @param string $prefix
     * @param string $suffix
     *
     * @return self
     */
    public function orWhereInExact(
        string $key,
        array $values,
        string $boolean = '|',
        string $operator = '=',
        string $prefix = '{',
        string $suffix = '}',
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix,
            $suffix);
    }

    /**
     * Add a "where not in" clause to the query.
     *
     * @param string $key
     * @param array  $values
     * @param string $boolean
     * @param string $operator
     * @param string $prefix
     * @param string $suffix
     *
     * @return self
     */
    public function whereNotIn(
        string $key,
        array $values,
        string $boolean = '&',
        string $operator = '!=',
        string $prefix = '(',
        string $suffix = ')',
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix,
            $suffix);
    }

    /**
     * Add an "or where not in" clause to the query.
     *
     * @param string $key
     * @param array  $values
     * @param string $boolean
     * @param string $operator
     * @param string $prefix
     * @param string $suffix
     *
     * @return self
     */
    public function orWhereNotIn(
        string $key,
        array $values,
        string $boolean = '|',
        string $operator = '!=',
        string $prefix = '(',
        string $suffix = ')'
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix,
            $suffix);
    }

    /**
     * Add a "where not in all" clause to the query.
     *
     * @param string $key
     * @param array  $values
     * @param string $boolean
     * @param string $operator
     * @param string $prefix
     * @param string $suffix
     *
     * @return self
     */
    public function whereNotInAll(
        string $key,
        array $values,
        string $boolean = '&',
        string $operator = '!=',
        string $prefix = '[',
        string $suffix = ']'
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix,
            $suffix);
    }

    /**
     * Add an "or where not in all" clause to the query.
     *
     * @param string $key
     * @param array  $values
     * @param string $boolean
     * @param string $operator
     * @param string $prefix
     * @param string $suffix
     *
     * @return self
     */
    public function orWhereNotInAll(
        string $key,
        array $values,
        string $boolean = '|',
        string $operator = '!=',
        string $prefix = '[',
        string $suffix = ']'
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix,
            $suffix);
    }

    /**
     * Add a "where not in exact" clause to the query.
     *
     * @param string $key
     * @param array  $values
     * @param string $boolean
     * @param string $operator
     * @param string $prefix
     * @param string $suffix
     *
     * @return self
     */
    public function whereNotInExact(
        string $key,
        array $values,
        string $boolean = '&',
        string $operator = '!=',
        string $prefix = '{',
        string $suffix = '}'
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix,
            $suffix);
    }

    /**
     * Add an "or where not in exact" clause to the query.
     *
     * @param string $key
     * @param array  $values
     * @param string $boolean
     * @param string $operator
     * @param string $prefix
     * @param string $suffix
     *
     * @return self
     */
    public function orWhereNotInExact(
        string $key,
        array $values,
        string $boolean = '|',
        string $operator = '!=',
        string $prefix = '{',
        string $suffix = '}'
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix,
            $suffix);
    }

    /**
     * Add a where between statement to the query.
     *
     * @param string $key
     * @param mixed  $first
     * @param mixed  $second
     * @param bool   $withBoundaries
     * @param string $boolean
     *
     * @return self
     * @throws ReflectionException|InvalidParamsException
     */
    public function whereBetween(
        string $key,
        mixed $first,
        mixed $second,
        bool $withBoundaries = true,
        string $boolean = '&'
    ): self {
        if (collect($this->dates)->has($key) && $this->dates[$key] === 'date') {
            $first = $this->castDate($first);
            $second = $this->castDate($second);
        }

        $this->whereNested(function ($query) use (
            $key,
            $first,
            $second,
            $withBoundaries
        ) {
            $operator = ($withBoundaries ? '=' : '');
            $query->where($key, '>' . $operator, $first)->where($key,
                '<' . $operator, $second);
        }, $boolean);

        return $this;
    }

    /**
     * Add a or where between statement to the query.
     *
     * @param string $key
     * @param mixed  $first
     * @param mixed  $second
     * @param bool   $withBoundaries
     * @param string $boolean
     *
     * @return self
     * @throws ReflectionException|InvalidParamsException
     */
    public function orWhereBetween(
        string $key,
        mixed $first,
        mixed $second,
        bool $withBoundaries = true,
        string $boolean = '|'
    ): self {
        return $this->whereBetween($key, $first, $second, $withBoundaries,
            $boolean);
    }

    /**
     * Add a where not between statement to the query.
     *
     * @param string $key
     * @param mixed  $first
     * @param mixed  $second
     * @param bool   $withBoundaries
     * @param string $boolean
     *
     * @return self
     * @throws ReflectionException|InvalidParamsException
     */
    public function whereNotBetween(
        string $key,
        mixed $first,
        mixed $second,
        bool $withBoundaries = false,
        string $boolean = '&'
    ): self {
        if (collect($this->dates)->has($key) && $this->dates[$key] === 'date') {
            $first = $this->castDate($first);
            $second = $this->castDate($second);
        }

        $this->whereNested(function ($query) use (
            $key,
            $first,
            $second,
            $withBoundaries
        ) {
            $operator = ($withBoundaries ? '=' : '');
            $query->where($key, '<' . $operator, $first)->orWhere($key,
                '>' . $operator, $second);
        }, $boolean);

        return $this;
    }

    /**
     * Add a or where not between statement to the query.
     *
     * @param string $key
     * @param mixed  $first
     * @param mixed  $second
     * @param bool   $withBoundaries
     * @param string $boolean
     *
     * @return self
     * @throws ReflectionException|InvalidParamsException
     */
    public function orWhereNotBetween(
        string $key,
        mixed $first,
        mixed $second,
        bool $withBoundaries = false,
        string $boolean = '|'
    ): self {
        return $this->whereNotBetween($key, $first, $second, $withBoundaries,
            $boolean);
    }

    /**
     * Add a "where has" statement to the query.
     *
     * @param string $relationship
     * @param string $boolean
     *
     * @return self
     */
    public function whereHas(string $relationship, string $boolean = '&'): self
    {
        $where = $this->query->get('where', new Collection());

        $currentWhere = $where;

        $where->push(($currentWhere->count() ? $boolean . ' ' : '') . $relationship . ' != null');

        $this->query->put('where', $where);

        return $this;
    }

    /**
     * Add an "or where has" statement to the query.
     *
     * @param string $relationship
     * @param string $boolean
     *
     * @return self
     */
    public function orWhereHas(string $relationship, string $boolean = '|'): self
    {
        return $this->whereHas($relationship, $boolean);
    }

    /**
     * Add a "where has not" statement to the query.
     *
     * @param string $relationship
     * @param string $boolean
     *
     * @return self
     */
    public function whereHasNot(string $relationship, string $boolean = '&'): self
    {
        $where = $this->query->get('where', new Collection());

        $currentWhere = $where;

        $where->push(($currentWhere->count() ? $boolean . ' ' : '') . $relationship . ' = null');

        $this->query->put('where', $where);

        return $this;
    }

    /**
     * Add a "where has not" statement to the query.
     *
     * @param string $relationship
     * @param string $boolean
     *
     * @return self
     */
    public function orWhereHasNot(string $relationship, string $boolean = '|'): self
    {
        return $this->whereHasNot($relationship, $boolean);
    }

    /**
     * Add a "where null" clause to the query.
     *
     * @param string $key
     * @param string $boolean
     *
     * @return self
     */
    public function whereNull(string $key, string $boolean = '&'): self
    {
        return $this->whereHasNot($key, $boolean);
    }

    /**
     * Add an "or where null" clause to the query.
     *
     * @param string $key
     * @param string $boolean
     *
     * @return self
     */
    public function orWhereNull(string $key, string $boolean = '|'): self
    {
        return $this->whereNull($key, $boolean);
    }

    /**
     * Add a "where not null" clause to the query.
     *
     * @param string $key
     * @param string $boolean
     *
     * @return self
     */
    public function whereNotNull(string $key, string $boolean = '&'): self
    {
        return $this->whereHas($key, $boolean);
    }

    /**
     * Add an "or where not null" clause to the query.
     *
     * @param string $key
     * @param string $boolean
     *
     * @return self
     */
    public function orWhereNotNull(string $key, string $boolean = '|'): self
    {
        return $this->whereNotNull($key, $boolean);
    }

    /**
     * Add a "where date" statement to the query.
     *
     * @param string     $key
     * @param mixed      $operator
     * @param mixed|null $value
     * @param string     $boolean
     *
     * @return self
     * @throws ReflectionException|JsonException|InvalidParamsException
     */
    public function whereDate(string $key, mixed $operator, mixed $value = null, string $boolean = '&'): self
    {
        [$value, $operator] = $this->prepareValueAndOperator($value, $operator,
            func_num_args() === 2);

        $start = Carbon::parse($value)->startOfDay()->timestamp;
        $end = Carbon::parse($value)->endOfDay()->timestamp;

        return match ($operator) {
            '>' => $this->whereDateGreaterThan($key, $operator, $value, $boolean),
            '>=' => $this->whereDateGreaterThanOrEquals($key, $operator, $value, $boolean),
            '<' => $this->whereDateLowerThan($key, $operator, $value, $boolean),
            '<=' => $this->whereDateLowerThanOrEquals($key, $operator, $value, $boolean),
            '!=' => $this->whereNotBetween($key, $start, $end, false, $boolean),
            default => $this->whereBetween($key, $start, $end, true, $boolean),
        };
    }

    /**
     * @param string $key
     * @param mixed  $operator
     * @param mixed  $value
     * @param string $boolean
     *
     * @return self
     * @throws JsonException|ReflectionException|InvalidParamsException
     */
    private function whereDateGreaterThan(string $key, mixed $operator, mixed $value, string $boolean): self
    {
        if (is_string($value) || $value instanceof DateTimeInterface) {
            $value = Carbon::parse($value)->addDay()->startOfDay()->timestamp;
        }

        return $this->where($key, $operator, $value, $boolean);
    }

    /**
     * @param string $key
     * @param mixed  $operator
     * @param mixed  $value
     * @param string $boolean
     *
     * @return self
     * @throws JsonException|ReflectionException|InvalidParamsException
     */
    private function whereDateGreaterThanOrEquals(string $key, mixed $operator, mixed $value, string $boolean): self
    {
        if (is_string($value) || $value instanceof DateTimeInterface) {
            $value = Carbon::parse($value)->startOfDay()->timestamp;
        }

        return $this->where($key, $operator, $value, $boolean);
    }

    /**
     * @param string $key
     * @param mixed  $operator
     * @param mixed  $value
     * @param string $boolean
     *
     * @return self
     * @throws JsonException|ReflectionException|InvalidParamsException
     */
    private function whereDateLowerThan(string $key, mixed $operator, mixed $value, string $boolean): self
    {
        if (is_string($value) || $value instanceof DateTimeInterface) {
            $value = Carbon::parse($value)->subDay()->endOfDay()->timestamp;
        }

        return $this->where($key, $operator, $value, $boolean);
    }

    /**
     * @param string $key
     * @param mixed  $operator
     * @param mixed  $value
     * @param string $boolean
     *
     * @return self
     * @throws JsonException
     * @throws ReflectionException|InvalidParamsException
     */
    private function whereDateLowerThanOrEquals(string $key, mixed $operator, mixed $value, string $boolean): self
    {
        if (is_string($value) || $value instanceof DateTimeInterface) {
            $value = Carbon::parse($value)->endOfDay()->timestamp;
        }

        return $this->where($key, $operator, $value, $boolean);
    }

    /**
     * Add an "or where date" statement to the query.
     *
     * @param string     $key
     * @param mixed      $operator
     * @param mixed|null $value
     * @param string     $boolean
     *
     * @return self
     * @throws ReflectionException|JsonException|InvalidParamsException
     */
    public function orWhereDate(string $key, mixed $operator, mixed $value = null, string $boolean = '|'): self
    {
        return $this->whereDate($key, $operator, $value, $boolean);
    }

    /**
     * Add a "where year" statement to the query.
     *
     * @param string     $key
     * @param mixed      $operator
     * @param mixed|null $value
     * @param string     $boolean
     *
     * @return self
     * @throws ReflectionException|JsonException|InvalidParamsException
     */
    public function whereYear(string $key, mixed $operator, mixed $value = null, string $boolean = '&'): self
    {
        [$value, $operator] = $this->prepareValueAndOperator($value, $operator,
            func_num_args() === 2);

        $value = Carbon::now()->setYear($value)->startOfYear();

        if ($operator === '=') {
            $start = $value->clone()->startOfYear()->timestamp;
            $end = $value->clone()->endOfYear()->timestamp;

            return $this->whereBetween($key, $start, $end, true, $boolean);
        }

        if ($operator === '>' || $operator === '<=') {
            $value = $value->clone()->endOfYear()->timestamp;
        } elseif ($operator === '>=' || $operator === '<') {
            $value = $value->clone()->startOfYear()->timestamp;
        }


        return $this->where($key, $operator, $value, $boolean);
    }

    /**
     * Add an "or where year" statement to the query.
     *
     * @param string     $key
     * @param mixed      $operator
     * @param mixed|null $value
     * @param string     $boolean
     *
     * @return self
     * @throws ReflectionException|JsonException|InvalidParamsException
     */
    public function orWhereYear(string $key, mixed $operator, mixed $value = null, string $boolean = '|'): self
    {
        [$value, $operator] = $this->prepareValueAndOperator($value, $operator,
            func_num_args() === 2);

        return $this->whereYear($key, $operator, $value, $boolean);
    }

    /**
     * Add a "sort" clause to the query.
     *
     * @param string $key
     * @param string $direction
     *
     * @return self
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
     * @param string $key
     *
     * @return self
     * @throws InvalidParamsException
     */
    public function orderByDesc(string $key): self
    {
        return $this->orderBy($key, 'desc');
    }

    /**
     * Add an "expand" clause to the query.
     *
     * @param array $relationships
     *
     * @return self
     */
    public function with(array $relationships): self
    {
        $relationships = (array)collect($relationships)->mapWithKeys(function (
            $fields,
            $relationship
        ) {
            if (is_numeric($relationship)) {
                return [$fields => ['*']];
            }
            return [$relationship => $fields];
        })->map(function ($fields, $relationship) {
            if (collect($fields)->count() === 0) {
                $fields = ['*'];
            }

            return collect($fields)->map(function ($field) use ($relationship) {
                return $relationship . '.' . $field;
            })->implode(',');
        })->values();

        $select = $this->query->get('fields', new Collection());

        $fields = collect($relationships)->flatten()->filter()->toArray();

        $this->query->put('fields', $select->push($fields)->flatten());

        return $this;
    }

    /**
     * Overwrite the cache lifetime for this query.
     *
     * @param int $seconds
     *
     * @return self
     */
    public function cache(int $seconds): self
    {
        $this->cacheLifetime = $seconds;

        return $this;
    }

    /**
     * Get the resulting query.
     *
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query->map(function ($value, $key) {
            if ($key === 'where') {
                return collect($value)->unique()->implode(' ');
            }
            if ($key === 'fields') {
                return collect($value)->unique()->sortBy(function ($field) {
                    return count(explode('.', $field));
                })->implode(',');
            }
            return $value;
        })->map(function ($value, $key) {
            return Str::finish($key . ' ' . $value, ';');
        })->unique()->sort()->implode("\n");
    }

    /**
     * Set the endpoint as string.
     *
     * @param string $endpoint
     *
     * @return self
     */
    public function endpoint(string $endpoint): self
    {
        if (!isset($this->class)) {
            $this->endpoint = $endpoint;
        }

        return $this;
    }

    /**
     * Set the endpoint from model or string
     *
     * @param mixed $model
     *
     * @return void
     * @throws ReflectionException
     * @throws InvalidParamsException
     */
    protected function setEndpoint(mixed $model): void
    {
        $neededNamespace = __NAMESPACE__ . '\\Models';

        if (is_object($model)) {
            $class = get_class($model);
            $parents = collect(class_parents($model));

            if ($parents->isEmpty()) {
                $parents->push($class);
            }

            if (is_bool($parents->last()) || is_null($parents->last())) {
                throw new InvalidParamsException('Last parent element is either null or false. String or {} required.');
            }

            $reflectionClass = new ReflectionClass($parents->last());
            $reflectionNamespace = $reflectionClass->getNamespaceName();

            if (Str::startsWith($reflectionNamespace, $neededNamespace)) {
                $this->class = get_class($model);
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
     *
     * @param mixed $date
     *
     * @return mixed
     */
    private function castDate(mixed $date): mixed
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
     * @return mixed
     * @throws MissingEndpointException
     */
    public function get(): mixed
    {
        $data = $this->fetchApi();

        if (isset($this->class) && $this->class) {
            $data = collect($data)->map(function ($result) {
                return $this->mapToModel($result);
            });
        }

        $this->init();

        return $data;
    }

    /**
     * @param mixed $result
     *
     * @return mixed
     */
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
     * @param int $id
     *
     * @return mixed
     * @throws MissingEndpointException|ReflectionException|JsonException|InvalidParamsException
     */
    public function find(int $id): mixed
    {
        return $this->where('id', $id)->first();
    }

    /**
     * @param int $id
     *
     * @return mixed
     * @throws MissingEndpointException|ModelNotFoundException|ReflectionException|JsonException|InvalidParamsException
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
     * @return mixed
     * @throws MissingEndpointException
     */
    public function first(): mixed
    {
        $data = $this->skip(0)->take(1)->get();

        return collect($data)->first();
    }

    /**
     * @return mixed
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
     * @return mixed
     * @throws MissingEndpointException
     */
    public function count(): mixed
    {
        $data = $this->fetchApi(true);

        $this->init();

        return $data;
    }

    /**
     * @return Collection
     * @throws MissingEndpointException
     */
    public function all(): Collection
    {
        $data = $this->skip(0)->take(500)->get();

        return collect($data);
    }

    /**
     * @param int $limit
     *
     * @return Paginator
     * @throws MissingEndpointException
     */
    public function paginate(int $limit = 10): Paginator
    {
        $page = 1;

        $request = request();

        if ($request instanceof Request) {
            $page = $request->get('page', 1);
        }

        if (!is_int($page) && !is_string($page)) {
            $page = 1;
        }

        $data = $this->forPage((int) $page, $limit)->get();

        return new Paginator($data, $limit);
    }

    /**
     * @param bool $count
     *
     * @return mixed
     * @throws MissingEndpointException
     */
    private function fetchApi(bool $count = false): mixed
    {
        if (!isset($this->endpoint)) {
            throw new MissingEndpointException();
        }

        $endpoint = $this->getEndpoint($count);

        $cacheKey = $this->handleCache($endpoint);

        return Cache::remember($cacheKey, $this->cacheLifetime, function () use ($endpoint, $count) {
            $response = $this->client->withHeaders([
                'Authorization' => 'Bearer ' . ApiHelper::retrieveAccessToken(),
            ])
                ->withBody($this->getQuery(), 'plain/text')
                ->retry(3, 100)
                ->post($endpoint)
                ->throw()
                ->json();

            if ($count && is_array($response)) {
                return $response['count'];
            }

            return $response;
        });
    }

    /**
     * @param bool $count
     *
     * @return string
     */
    private function getEndpoint(bool $count = false): string
    {
        $endpoint = $this->endpoint;

        if ($count) {
            $endpoint = Str::finish($endpoint, '/count');
        }

        return $endpoint;
    }

    /**
     * @param string $endpoint
     *
     * @return string
     */
    private function handleCache(string $endpoint): string
    {
        $cacheKey = 'igdb_cache.' . md5($endpoint . $this->getQuery());

        if ($this->cacheLifetime === 0) {
            Cache::forget($cacheKey);
        }

        return $cacheKey;
    }
}
