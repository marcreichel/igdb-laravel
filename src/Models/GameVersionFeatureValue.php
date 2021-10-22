<?php

namespace MarcReichel\IGDBLaravel\Models;

class GameVersionFeatureValue extends Model
{
    protected array $casts = [
        'game_feature' => GameVersionFeature::class,
    ];
}
