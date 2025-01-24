<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Traits;

use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * @internal
 */
trait HasWhereIn
{
    /**
     * Add a "where in" clause to the query.
     */
    public function whereIn(
        string $key,
        array $values,
        string $boolean = '&',
        string $operator = '=',
        string $prefix = '(',
        string $suffix = ')',
    ): self {
        if (($prefix === '(' && $suffix !== ')') || ($prefix === '[' && $suffix !== ']') || ($prefix === '{' && $suffix !== '}')) {
            $message = 'Prefix and Suffix must be "(" and ")", "[" and "]" or "{" and "}".';

            throw new InvalidArgumentException($message);
        }

        $where = $this->query->get('where', new Collection());

        $valuesString = collect($values)->map(static fn (mixed $value) => !is_numeric($value) ? '"' . $value . '"' : $value)->implode(',');

        $where->push(($where->count() ? $boolean . ' ' : '') . $key . ' ' . $operator . ' ' . $prefix . $valuesString . $suffix);

        $this->query->put('where', $where);

        return $this;
    }

    /**
     * Add an "or where in" clause to the query.
     */
    public function orWhereIn(
        string $key,
        array $value,
        string $boolean = '|',
        string $operator = '=',
        string $prefix = '(',
        string $suffix = ')',
    ): self {
        return $this->whereIn($key, $value, $boolean, $operator, $prefix, $suffix);
    }

    /**
     * Add a "where in all" clause to the query.
     */
    public function whereInAll(
        string $key,
        array $values,
        string $boolean = '&',
        string $operator = '=',
        string $prefix = '[',
        string $suffix = ']',
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix, $suffix);
    }

    /**
     * Add an "or where in all" clause to the query.
     */
    public function orWhereInAll(
        string $key,
        array $values,
        string $boolean = '|',
        string $operator = '=',
        string $prefix = '[',
        string $suffix = ']',
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix, $suffix);
    }

    /**
     * Add a "where in exact" clause to the query.
     */
    public function whereInExact(
        string $key,
        array $values,
        string $boolean = '&',
        string $operator = '=',
        string $prefix = '{',
        string $suffix = '}',
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix, $suffix);
    }

    /**
     * Add an "or where in exact" clause to the query.
     */
    public function orWhereInExact(
        string $key,
        array $values,
        string $boolean = '|',
        string $operator = '=',
        string $prefix = '{',
        string $suffix = '}',
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix, $suffix);
    }

    /**
     * Add a "where not in" clause to the query.
     */
    public function whereNotIn(
        string $key,
        array $values,
        string $boolean = '&',
        string $operator = '!=',
        string $prefix = '(',
        string $suffix = ')',
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix, $suffix);
    }

    /**
     * Add an "or where not in" clause to the query.
     */
    public function orWhereNotIn(
        string $key,
        array $values,
        string $boolean = '|',
        string $operator = '!=',
        string $prefix = '(',
        string $suffix = ')',
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix, $suffix);
    }

    /**
     * Add a "where not in all" clause to the query.
     */
    public function whereNotInAll(
        string $key,
        array $values,
        string $boolean = '&',
        string $operator = '!=',
        string $prefix = '[',
        string $suffix = ']',
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix, $suffix);
    }

    /**
     * Add an "or where not in all" clause to the query.
     */
    public function orWhereNotInAll(
        string $key,
        array $values,
        string $boolean = '|',
        string $operator = '!=',
        string $prefix = '[',
        string $suffix = ']',
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix, $suffix);
    }

    /**
     * Add a "where not in exact" clause to the query.
     */
    public function whereNotInExact(
        string $key,
        array $values,
        string $boolean = '&',
        string $operator = '!=',
        string $prefix = '{',
        string $suffix = '}',
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix, $suffix);
    }

    /**
     * Add an "or where not in exact" clause to the query.
     */
    public function orWhereNotInExact(
        string $key,
        array $values,
        string $boolean = '|',
        string $operator = '!=',
        string $prefix = '{',
        string $suffix = '}',
    ): self {
        return $this->whereIn($key, $values, $boolean, $operator, $prefix, $suffix);
    }
}
