<?php

namespace MarcReichel\IGDBLaravel\Models;

class GameEngine extends Model
{
    /**
     * @var array|string[]
     */
    protected array $casts = [
        'logo' => GameEngineLogo::class,
    ];
}
