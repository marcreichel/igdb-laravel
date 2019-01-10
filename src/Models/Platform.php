<?php

namespace MarcReichel\IGDBLaravel\Models;


class Platform extends Model
{
    protected $casts = [
        'websites' => PlatformWebsite::class,
    ];
}
