<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class ReleaseDate extends Model
{
    protected array $casts = [
        'game' => Game::class,
        'platform' => Platform::class,
        'release_region' => ReleaseDateRegion::class,
        'status' => ReleaseDateStatus::class,
    ];
}
