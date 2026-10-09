<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class AgeRatingCategory extends Model
{
    protected array $casts = [
        'organization' => AgeRatingOrganization::class,
    ];
}
