<?php

namespace MarcReichel\IGDBLaravel\Traits;

trait Operators
{
    /**
     * All of the available clause operators.
     *
     * @var array
     */
    public $operators = [
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
