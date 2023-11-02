<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\CollectionType;

class CollectionTypeCreated extends Event
{
    public CollectionType $data;

    public function __construct(CollectionType $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
