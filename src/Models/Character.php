<?php

namespace MarcReichel\IGDBLaravel\Models;

class Character extends Model
{
    protected array $casts = [
        'mug_shot' => CharacterMugShot::class,
    ];
}
