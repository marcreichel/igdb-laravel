<?php

namespace MarcReichel\IGDBLaravel\Models;

class GameVersionFeatureValue extends Model
{
    /**
     * @var array|string[]
     */
    protected array $casts = [
        'game_feature' => GameVersionFeature::class,
    ];
}
