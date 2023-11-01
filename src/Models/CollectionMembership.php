<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class CollectionMembership extends Model
{
    protected array $casts = [
        'collection' => Collection::class,
        'game' => Game::class,
        'type' => CollectionMembershipType::class,
    ];
}
