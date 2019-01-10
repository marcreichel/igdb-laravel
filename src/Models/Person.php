<?php

namespace MarcReichel\IGDBLaravel\Models;


class Person extends Model
{
    public $privateEndpoint = true;
    protected $endpoint = 'private/people';

    protected $casts = [
        'credited_games' => Game::class,
        'mug_shot' => PersonMugShot::class,
        'parent' => self::class,
        'voice_acted' => Game::class,
        'websites' => PersonWebsite::class,
    ];
}
