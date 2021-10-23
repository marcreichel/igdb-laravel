<?php

namespace MarcReichel\IGDBLaravel\Models;

class AgeRating extends Model
{
    /**
     * @var array|string[]
     */
    protected array $casts = [
        'content_descriptions' => AgeRatingContentDescription::class,
    ];
}
