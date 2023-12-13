<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class Franchise extends Model
{
    protected array $casts = [
        'games' => Game::class,
    ];
}
