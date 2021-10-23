<?php

namespace MarcReichel\IGDBLaravel\Models;

class Platform extends Model
{
    /**
     * @var array|string[]
     */
    protected array $casts = [
        'websites' => PlatformWebsite::class,
    ];
}
