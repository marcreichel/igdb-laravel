<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class PopularityPrimitive extends Model
{
    protected array $casts = [
        'external_popularity_source' => ExternalGameSource::class,
        'popularity_type' => PopularityType::class,
    ];
}
