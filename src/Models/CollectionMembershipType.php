<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class CollectionMembershipType extends Model
{
    protected array $casts = [
        'allowed_collection_type' => CollectionType::class,
    ];
}
