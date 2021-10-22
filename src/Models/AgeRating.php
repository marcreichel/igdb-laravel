<?php

namespace MarcReichel\IGDBLaravel\Models;

class AgeRating extends Model
{
    protected array $casts = [
        'content_descriptions' => AgeRatingContentDescription::class,
    ];
}
