<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class ExternalGame extends Model
{
    protected array $casts = [
        'game' => Game::class,
        'platform' => Platform::class,
    ];
}
