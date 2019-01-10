<?php

namespace MarcReichel\IGDBLaravel\Models;


class AgeRating extends Model
{
    protected $casts = [
        'content_descriptions' => AgeRatingContentDescription::class,
    ];
}
