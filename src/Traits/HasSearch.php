<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Traits;

use JsonException;
use MarcReichel\IGDBLaravel\Builder;
use MarcReichel\IGDBLaravel\Exceptions\InvalidParamsException;
use ReflectionException;

/**
 * @internal
 */
trait HasSearch
{
    /**
     * Search for the given string.
     */
    public function search(string $query): self
    {
        $this->query->put('search', '"' . $query . '"');

        return $this;
    }

    /**
     * Add a fuzzy search to the query.
     *
     * @throws ReflectionException
     * @throws InvalidParamsException
     * @throws JsonException
     */
    public function fuzzySearch(
        mixed $key,
        string $query,
        bool $caseSensitive = false,
        string $boolean = '&',
    ): self {
        $tokenizedQuery = explode(' ', $query);
        $keys = collect($key)->crossJoin($tokenizedQuery)->toArray();

        return $this->whereNested(static function (Builder $query) use ($keys, $caseSensitive): void {
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
     * @throws ReflectionException|InvalidParamsException|JsonException
     */
    public function orFuzzySearch(
        mixed $key,
        string $query,
        bool $caseSensitive = false,
        string $boolean = '|',
    ): self {
        return $this->fuzzySearch($key, $query, $caseSensitive, $boolean);
    }
}
