<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Traits;

use JsonException;
use MarcReichel\IGDBLaravel\Builder;
use MarcReichel\IGDBLaravel\Exceptions\InvalidParamsException;
use ReflectionException;

trait HasWhereBetween
{
    /**
     * Add a where between statement to the query.
     *
     * @throws ReflectionException
     * @throws InvalidParamsException
     * @throws JsonException
     */
    public function whereBetween(
        string $key,
        mixed $first,
        mixed $second,
        bool $withBoundaries = true,
        string $boolean = '&',
    ): self {
        if (collect($this->dates)->has($key) && $this->dates[$key] === 'date') {
            $first = $this->castDate($first);
            $second = $this->castDate($second);
        }

        $this->whereNested(static function (Builder $query) use (
            $key,
            $first,
            $second,
            $withBoundaries,
        ): void {
            $operator = ($withBoundaries ? '=' : '');
            $query->where($key, '>' . $operator, $first)->where($key, '<' . $operator, $second);
        }, $boolean);

        return $this;
    }

    /**
     * Add a or where between statement to the query.
     *
     * @throws ReflectionException
     * @throws InvalidParamsException
     * @throws JsonException
     */
    public function orWhereBetween(
        string $key,
        mixed $first,
        mixed $second,
        bool $withBoundaries = true,
        string $boolean = '|',
    ): self {
        return $this->whereBetween($key, $first, $second, $withBoundaries, $boolean);
    }

    /**
     * Add a where not between statement to the query.
     *
     * @throws ReflectionException
     * @throws InvalidParamsException
     * @throws JsonException
     */
    public function whereNotBetween(
        string $key,
        mixed $first,
        mixed $second,
        bool $withBoundaries = false,
        string $boolean = '&',
    ): self {
        if (collect($this->dates)->has($key) && $this->dates[$key] === 'date') {
            $first = $this->castDate($first);
            $second = $this->castDate($second);
        }

        $this->whereNested(static function (Builder $query) use (
            $key,
            $first,
            $second,
            $withBoundaries,
        ): void {
            $operator = ($withBoundaries ? '=' : '');
            $query->where($key, '<' . $operator, $first)->orWhere($key, '>' . $operator, $second);
        }, $boolean);

        return $this;
    }

    /**
     * Add a or where not between statement to the query.
     *
     * @throws ReflectionException
     * @throws InvalidParamsException
     * @throws JsonException
     */
    public function orWhereNotBetween(
        string $key,
        mixed $first,
        mixed $second,
        bool $withBoundaries = false,
        string $boolean = '|',
    ): self {
        return $this->whereNotBetween($key, $first, $second, $withBoundaries, $boolean);
    }
}
