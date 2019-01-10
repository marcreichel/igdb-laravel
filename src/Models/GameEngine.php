<?php

namespace MarcReichel\IGDBLaravel\Models;


class GameEngine extends Model
{
    protected $casts = [
        'logo' => GameEngineLogo::class,
    ];
}
