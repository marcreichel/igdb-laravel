<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Traits;

/**
 * @internal
 */
trait HasLimits
{
    /**
     * Set the "limit" value of the query.
     */
    public function limit(int $limit): self
    {
        $limit = min($limit, 500);
        $this->query->put('limit', $limit);

        return $this;
    }

    /**
     * Alias to set the "limit" value of the query.
     */
    public function take(int $limit): self
    {
        return $this->limit($limit);
    }

    /**
     * Set the "offset" value of the query.
     */
    public function offset(int $offset): self
    {
        $this->query->put('offset', $offset);

        return $this;
    }

    /**
     * Alias to set the "offset" value of the query.
     */
    public function skip(int $offset): self
    {
        return $this->offset($offset);
    }

    /**
     * Set the limit and offset for a given page.
     */
    public function forPage(int $page, int $perPage = 10): self
    {
        return $this->skip(($page - 1) * $perPage)->take($perPage);
    }
}
