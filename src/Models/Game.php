<?php

namespace MarcReichel\IGDBLaravel\Models;


class Game extends Model
{
    protected $casts = [
        'bundles' => self::class,
        'dlcs' => self::class,
        'expansions' => self::class,
        'parent_game' => self::class,
        'similar_games' => self::class,
        'standalone_expansions' => self::class,
        'version_parent' => self::class,
    ];
}
