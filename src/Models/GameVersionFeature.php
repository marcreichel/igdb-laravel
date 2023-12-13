<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class GameVersionFeature extends Model
{
    protected array $casts = [
        'values' => GameVersionFeatureValue::class,
    ];
}
