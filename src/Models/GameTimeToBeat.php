<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class GameTimeToBeat extends Model
{
    protected array $casts = [
        'game' => Game::class,
    ];
}
