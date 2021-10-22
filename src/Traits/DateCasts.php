<?php

namespace MarcReichel\IGDBLaravel\Traits;

trait DateCasts
{
    /**
     * These fields should be cast.
     *
     * @var array
     */
    public array $dates = [
        'created_at' => 'date',
        'updated_at' => 'date',
        'change_date' => 'date',
        'start_date' => 'date',
        'published_at' => 'date',
        'first_release_date' => 'date',
    ];
}
