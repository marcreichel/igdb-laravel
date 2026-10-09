<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class AgeRatingContentDescriptionV2 extends Model
{
    protected array $casts = [
        'description_type' => AgeRatingContentDescriptionType::class,
        'organization' => AgeRatingOrganization::class,
    ];

    protected function setEndpoint(): void
    {
        $this->endpoint = 'age_rating_content_descriptions_v2';
    }
}
