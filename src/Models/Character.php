<?php

namespace MarcReichel\IGDBLaravel\Models;


class Character extends Model
{
    protected $casts = [
        'mug_shot' => CharacterMugShot::class,
    ];
}
