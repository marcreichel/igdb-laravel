<?php

namespace MarcReichel\IGDBLaravel\Models;

class GameVersionFeature extends Model
{
    protected array $casts = [
        'values' => GameVersionFeatureValue::class,
    ];
}
