<?php

namespace MarcReichel\IGDBLaravel\Traits;

trait HasAttributes
{
    public array $attributes = [];
    protected array $casts = [];
    protected array $dates = [
        'created_at',
        'updated_at',
        'change_date',
        'start_date',
        'published_at',
        'first_release_date'
    ];
}
