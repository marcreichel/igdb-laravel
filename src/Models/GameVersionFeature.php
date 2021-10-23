<?php

namespace MarcReichel\IGDBLaravel\Models;

class GameVersionFeature extends Model
{
    /**
     * @var array|string[]
     */
    protected array $casts = [
        'values' => GameVersionFeatureValue::class,
    ];
}
