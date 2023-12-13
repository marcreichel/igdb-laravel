<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class Event extends Model
{
    protected array $casts = [
        'event_logo' => EventLogo::class,
        'games' => Game::class,
        'videos' => GameVideo::class,
    ];
}
