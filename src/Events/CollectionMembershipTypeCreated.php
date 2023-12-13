<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\CollectionMembershipType;

class CollectionMembershipTypeCreated extends Event
{
    public CollectionMembershipType $data;

    public function __construct(CollectionMembershipType $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
