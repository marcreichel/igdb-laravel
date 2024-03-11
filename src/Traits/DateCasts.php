<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Traits;

/**
 * @internal
 */
trait DateCasts
{
    /**
     * These fields should be cast.
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
