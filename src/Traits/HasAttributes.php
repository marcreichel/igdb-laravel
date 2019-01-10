<?php

namespace MarcReichel\IGDBLaravel\Traits;


trait HasAttributes
{
    public $attributes = [];
    protected $casts = [];
    protected $dates = [
        'created_at',
        'updated_at',
        'change_date',
        'start_date',
        'published_at',
        'first_release_date'
    ];
}
