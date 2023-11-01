<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class GameEngine extends Model
{
    protected array $casts = [
        'companies' => Company::class,
        'logo' => GameEngineLogo::class,
        'platforms' => Platform::class,
    ];
}
