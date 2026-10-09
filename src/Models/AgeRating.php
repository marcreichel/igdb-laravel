<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class AgeRating extends Model
{
    protected array $casts = [
        'content_descriptions' => AgeRatingContentDescription::class, // @deprecated upstream
        'rating_category' => AgeRatingCategory::class,
        'rating_content_descriptions' => AgeRatingContentDescriptionV2::class,
    ];
}
