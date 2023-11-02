<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\CollectionRelationType;

class CollectionRelationTypeCreated extends Event
{
    public CollectionRelationType $data;

    public function __construct(CollectionRelationType $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
