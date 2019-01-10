<?php

namespace MarcReichel\IGDBLaravel\Models;


class Feed extends Model
{
    protected $casts = [
        'feed_video' => GameVideo::class,
    ];
}
