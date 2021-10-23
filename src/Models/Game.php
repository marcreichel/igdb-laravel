<?php

namespace MarcReichel\IGDBLaravel\Models;

class Game extends Model
{
    /**
     * @var array|string[]
     */
    protected array $casts = [
        'bundles' => self::class,
        'dlcs' => self::class,
        'expansions' => self::class,
        'parent_game' => self::class,
        'remakes' => self::class,
        'remasters' => self::class,
        'similar_games' => self::class,
        'standalone_expansions' => self::class,
        'version_parent' => self::class,
    ];
}
