<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class Character extends Model
{
    protected array $casts = [
        'character_species' => CharacterSpecie::class,
        'games' => Game::class,
        'mug_shot' => CharacterMugShot::class,
    ];
}
