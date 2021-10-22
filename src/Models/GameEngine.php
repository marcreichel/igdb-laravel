<?php

namespace MarcReichel\IGDBLaravel\Models;

class GameEngine extends Model
{
    protected array $casts = [
        'logo' => GameEngineLogo::class,
    ];
}
