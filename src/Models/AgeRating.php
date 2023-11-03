<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class AgeRating extends Model
{
    protected array $casts = [
        'content_descriptions' => AgeRatingContentDescription::class,
    ];
}
