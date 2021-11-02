<?php

namespace MarcReichel\IGDBLaravel\Traits;

trait HasAttributes
{
    /**
     * @var array
     */
    public array $attributes = [];

    /**
     * @var array
     */
    protected array $casts = [];

    /**
     * @var string[]
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
