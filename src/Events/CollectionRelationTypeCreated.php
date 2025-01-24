<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\CollectionRelationType;

class CollectionRelationTypeCreated extends Event
{
    public function __construct(public CollectionRelationType $data, Request $request)
    {
        parent::__construct($request);
    }
}
