<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Traits;

use InvalidArgumentException;

/**
 * @internal
 */
trait ValuePreparer
{
    /**
     * Prepare the value and operator for a where clause.
     */
    private function prepareValueAndOperator(
        mixed $value,
        mixed $operator,
        bool $useDefault = false,
    ): array {
        if ($useDefault) {
            return [$operator, '='];
        }

        if (!is_string($operator) && null !== $operator) {
            throw new InvalidArgumentException('Parameter #2 $operator needs to be string or null. ' . gettype($operator) . ' given.');
        }

        if ($this->invalidOperatorAndValue($operator, $value)) {
            throw new InvalidArgumentException('Illegal operator and value combination.');
        }

        return [$value, $operator];
    }

    /**
     * Determine if the given operator and value combination is legal.
     *
     * Prevents using Null values with invalid operators.
     */
    private function invalidOperatorAndValue(?string $operator, mixed $value): bool
    {
        return null === $value && in_array($operator, $this->operators, true) && !in_array($operator, ['=', '!=']);
    }
}
