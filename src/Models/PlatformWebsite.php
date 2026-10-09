<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class PlatformWebsite extends Model
{
    protected array $casts = [
        'type' => WebsiteType::class,
    ];
}
