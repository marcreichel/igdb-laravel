<?php

namespace MarcReichel\IGDBLaravel\Models;


class Review extends Model
{
    public $privateEndpoint = true;

    protected $casts = [
        'user_rating' => Rate::class,
        'video' => ReviewVideo::class,
    ];
}
