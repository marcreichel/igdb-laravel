<?php

namespace MarcReichel\IGDBLaravel\Models;

class Platform extends Model
{
    protected array $casts = [
        'websites' => PlatformWebsite::class,
    ];
}
