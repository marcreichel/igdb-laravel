<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\EntityType;

class EntityTypeCreated extends Event
{
    public function __construct(public EntityType $data, Request $request)
    {
        parent::__construct($request);
    }
}
