<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Traits;

/**
 * @internal
 */
trait HasSelect
{
    /**
     * Set the fields to be selected.
     */
    public function select(mixed $fields): self
    {
        $fields = is_array($fields) ? $fields : func_get_args();
        $collection = collect($fields);

        if ($collection->isEmpty()) {
            $collection->push('*');
        }

        $collection = $collection->filter(static fn (string $field) => !strpos($field, '.'))->flatten();

        if ($collection->isEmpty()) {
            $collection->push('*');
        }

        $this->query->put('fields', $collection);

        return $this;
    }
}
