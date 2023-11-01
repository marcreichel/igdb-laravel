<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class Collection extends Model
{
    protected array $casts = [
        'as_child_relations' => CollectionRelation::class,
        'as_parent_relations' => CollectionRelation::class,
        'type' => CollectionType::class,
    ];
}
