<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JsonException;

/**
 * @internal
 */
trait HasWhereLike
{
    /**
     * Add a "where like" clause to the query.
     *
     * @throws JsonException
     */
    public function whereLike(
        string $key,
        string $value,
        bool $caseSensitive = true,
        string $boolean = '&',
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
     * @throws JsonException
     */
    public function orWhereLike(
        string $key,
        string $value,
        bool $caseSensitive = true,
        string $boolean = '|',
    ): self {
        return $this->whereLike($key, $value, $caseSensitive, $boolean);
    }

    /**
     * Add a "where not like" clause to the query.
     *
     * @throws JsonException
     */
    public function whereNotLike(
        string $key,
        string $value,
        bool $caseSensitive = true,
        string $boolean = '&',
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
     * @throws JsonException
     */
    public function orWhereNotLike(
        string $key,
        string $value,
        bool $caseSensitive = true,
        string $boolean = '|',
    ): self {
        return $this->whereNotLike($key, $value, $caseSensitive, $boolean);
    }

    /**
     * @throws JsonException
     */
    private function generateWhereLikeClause(
        string $key,
        string $value,
        bool $caseSensitive,
        string $operator,
        string $insensitiveOperator,
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
}
