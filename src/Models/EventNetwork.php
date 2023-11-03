<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class EventNetwork extends Model
{
    protected array $casts = [
        'event' => Event::class,
        'network_type' => NetworkType::class,
    ];
}
