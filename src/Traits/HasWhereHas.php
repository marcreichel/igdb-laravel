<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Traits;

use Illuminate\Support\Collection;

/**
 * @internal
 */
trait HasWhereHas
{
    /**
     * Add a "where has" statement to the query.
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
     */
    public function orWhereHas(string $relationship, string $boolean = '|'): self
    {
        return $this->whereHas($relationship, $boolean);
    }

    /**
     * Add a "where has not" statement to the query.
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
     */
    public function orWhereHasNot(string $relationship, string $boolean = '|'): self
    {
        return $this->whereHasNot($relationship, $boolean);
    }

    /**
     * Add a "where null" clause to the query.
     */
    public function whereNull(string $key, string $boolean = '&'): self
    {
        return $this->whereHasNot($key, $boolean);
    }

    /**
     * Add an "or where null" clause to the query.
     */
    public function orWhereNull(string $key, string $boolean = '|'): self
    {
        return $this->whereNull($key, $boolean);
    }

    /**
     * Add a "where not null" clause to the query.
     */
    public function whereNotNull(string $key, string $boolean = '&'): self
    {
        return $this->whereHas($key, $boolean);
    }

    /**
     * Add an "or where not null" clause to the query.
     */
    public function orWhereNotNull(string $key, string $boolean = '|'): self
    {
        return $this->whereNotNull($key, $boolean);
    }
}
