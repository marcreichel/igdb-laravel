<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\CollectionRelation;

class CollectionRelationCreated extends Event
{
    public function __construct(public CollectionRelation $data, Request $request)
    {
        parent::__construct($request);
    }
}
