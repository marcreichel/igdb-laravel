<?php

namespace MarcReichel\IGDBLaravel\Models;


class GameVersionFeatureValue extends Model
{
    protected $casts = [
        'game_feature' => GameVersionFeature::class,
    ];
}
