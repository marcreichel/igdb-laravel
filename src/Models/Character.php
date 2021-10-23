<?php

namespace MarcReichel\IGDBLaravel\Models;

class Character extends Model
{
    /**
     * @var array|string[]
     */
    protected array $casts = [
        'mug_shot' => CharacterMugShot::class,
    ];
}
