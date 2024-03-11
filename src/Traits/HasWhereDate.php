<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Traits;

use Carbon\Carbon;
use DateTimeInterface;
use JsonException;
use MarcReichel\IGDBLaravel\Exceptions\InvalidParamsException;
use ReflectionException;

/**
 * @internal
 */
trait HasWhereDate
{
    /**
     * Add a "where date" statement to the query.
     *
     * @throws ReflectionException
     * @throws JsonException
     * @throws InvalidParamsException
     */
    public function whereDate(string $key, mixed $operator, mixed $value = null, string $boolean = '&'): self
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value,
            $operator,
            func_num_args() === 2,
        );

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
     * @throws JsonException
     * @throws ReflectionException
     * @throws InvalidParamsException
     */
    private function whereDateGreaterThan(string $key, mixed $operator, mixed $value, string $boolean): self
    {
        if (is_string($value) || $value instanceof DateTimeInterface) {
            $value = Carbon::parse($value)->addDay()->startOfDay()->timestamp;
        }

        return $this->where($key, $operator, $value, $boolean);
    }

    /**
     * @throws JsonException
     * @throws ReflectionException
     * @throws InvalidParamsException
     */
    private function whereDateGreaterThanOrEquals(string $key, mixed $operator, mixed $value, string $boolean): self
    {
        if (is_string($value) || $value instanceof DateTimeInterface) {
            $value = Carbon::parse($value)->startOfDay()->timestamp;
        }

        return $this->where($key, $operator, $value, $boolean);
    }

    /**
     * @throws JsonException
     * @throws ReflectionException
     * @throws InvalidParamsException
     */
    private function whereDateLowerThan(string $key, mixed $operator, mixed $value, string $boolean): self
    {
        if (is_string($value) || $value instanceof DateTimeInterface) {
            $value = Carbon::parse($value)->subDay()->endOfDay()->timestamp;
        }

        return $this->where($key, $operator, $value, $boolean);
    }

    /**
     * @throws JsonException
     * @throws ReflectionException
     * @throws InvalidParamsException
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
     * @throws ReflectionException
     * @throws JsonException
     * @throws InvalidParamsException
     */
    public function orWhereDate(string $key, mixed $operator, mixed $value = null, string $boolean = '|'): self
    {
        return $this->whereDate($key, $operator, $value, $boolean);
    }

    /**
     * Add a "where year" statement to the query.
     *
     * @throws ReflectionException
     * @throws JsonException
     * @throws InvalidParamsException
     */
    public function whereYear(string $key, mixed $operator, mixed $value = null, string $boolean = '&'): self
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value,
            $operator,
            func_num_args() === 2,
        );

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
     * @throws ReflectionException
     * @throws JsonException
     * @throws InvalidParamsException
     */
    public function orWhereYear(string $key, mixed $operator, mixed $value = null, string $boolean = '|'): self
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value,
            $operator,
            func_num_args() === 2,
        );

        return $this->whereYear($key, $operator, $value, $boolean);
    }
}
