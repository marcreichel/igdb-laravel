<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class PopularityType extends Model
{
    protected array $casts = [
        'external_popularity_source' => ExternalGameSource::class,
    ];
}
