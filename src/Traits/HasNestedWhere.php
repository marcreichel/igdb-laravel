<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Traits;

use Closure;
use Illuminate\Support\Collection;
use MarcReichel\IGDBLaravel\Builder;
use MarcReichel\IGDBLaravel\Exceptions\InvalidParamsException;
use ReflectionException;

/**
 * @internal
 */
trait HasNestedWhere
{
    /**
     * Add a nested where statement to the query.
     *
     * @throws ReflectionException
     * @throws InvalidParamsException
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
     */
    protected function addNestedWhereQuery(Builder $query, string $boolean): self
    {
        $where = $this->query->get('where', new Collection());

        $nested = '(' . $query->query->get('where', new Collection())->implode(' ') . ')';

        $where->push(($where->count() ? $boolean . ' ' : '') . $nested);

        $this->query->put('where', $where);

        return $this;
    }
}
