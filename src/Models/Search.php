<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class Search extends Model
{
    protected array $casts = [
        'character' => Character::class,
        'collection' => Collection::class,
        'company' => Company::class,
        'game' => Game::class,
        'platform' => Platform::class,
        'theme' => Theme::class,
    ];
}
