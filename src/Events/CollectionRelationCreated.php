<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\CollectionRelation;

class CollectionRelationCreated extends Event
{
    public CollectionRelation $data;

    public function __construct(CollectionRelation $data, Request $request)
    {
        parent::__construct($request);

        $this->data = $data;
    }
}
