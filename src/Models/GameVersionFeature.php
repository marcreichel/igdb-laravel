<?php

namespace MarcReichel\IGDBLaravel\Models;


class GameVersionFeature extends Model
{
    protected $casts = [
        'values' => GameVersionFeatureValue::class,
    ];
}
