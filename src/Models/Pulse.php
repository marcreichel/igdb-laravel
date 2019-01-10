<?php

namespace MarcReichel\IGDBLaravel\Models;


class Pulse extends Model
{
    protected $casts = [
        'websites' => PulseUrl::class,
    ];
}
