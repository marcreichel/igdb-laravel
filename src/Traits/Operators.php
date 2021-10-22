<?php

namespace MarcReichel\IGDBLaravel\Traits;

trait Operators
{
    /**
     * All the available clause operators.
     *
     * @var array
     */
    public array $operators = [
        '=',
        '<',
        '>',
        '<=',
        '>=',
        '!=',
        '!=',
        '~',
        'like',
        'ilike',
        'not like',
        'not ilike',
    ];
}
