<?php

namespace MarcReichel\IGDBLaravel\Models;


class Credit extends Model
{
    public $privateEndpoint = true;

    protected $casts = [
        'person_title' => Title::class,
    ];
}
