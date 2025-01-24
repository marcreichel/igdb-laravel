<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Traits;

use Closure;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use JsonException;
use MarcReichel\IGDBLaravel\Builder;
use MarcReichel\IGDBLaravel\Exceptions\InvalidParamsException;
use ReflectionException;

/**
 * @internal
 */
trait HasWhere
{
    /**
     * Add a basic where clause to the query.
     *
     * @throws ReflectionException
     * @throws JsonException
     * @throws InvalidParamsException
     */
    public function where(
        mixed $key,
        mixed $operator = null,
        mixed $value = null,
        string $boolean = '&',
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

        [$value, $operator] = $this->prepareValueAndOperator(
            $value,
            $operator,
            func_num_args() === 2,
        );

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
     * @throws ReflectionException
     * @throws JsonException
     * @throws InvalidParamsException
     */
    public function orWhere(
        mixed $key,
        string $operator = null,
        mixed $value = null,
        string $boolean = '|',
    ): self {
        if ($key instanceof Closure) {
            return $this->whereNested($key, $boolean);
        }

        if (is_array($key)) {
            return $this->addArrayOfWheres($key, $boolean);
        }

        [$value, $operator] = $this->prepareValueAndOperator(
            $value,
            $operator,
            func_num_args() === 2,
        );

        return $this->where($key, $operator, $value, $boolean);
    }

    /**
     * Add an array of where clauses to the query.
     *
     * @throws ReflectionException
     * @throws InvalidParamsException
     */
    protected function addArrayOfWheres(
        array $arrayOfWheres,
        string $boolean,
        string $method = 'where',
    ): self {
        return $this->whereNested(static function (Builder $query) use (
            $arrayOfWheres,
            $method,
            $boolean
        ): void {
            foreach ($arrayOfWheres as $key => $value) {
                if (is_numeric($key) && is_array($value)) {
                    $query->$method(...array_values($value));
                } else {
                    $query->$method($key, '=', $value, $boolean);
                }
            }
        }, $boolean);
    }
}
