<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class Cover extends Image
{
    protected array $casts = [
        'game' => Game::class,
        'game_localization' => GameLocalization::class,
    ];
}
