<?php

namespace MarcReichel\IGDBLaravel\Traits;

trait HasAttributes
{
    /**
     * @var array|string[]
     */
    public array $attributes = [];

    /**
     * @var array|string[]
     */
    protected array $casts = [];

    /**
     * @var array|string[]
     */
    protected array $dates = [
        'created_at',
        'updated_at',
        'change_date',
        'start_date',
        'published_at',
        'first_release_date',
    ];
}
