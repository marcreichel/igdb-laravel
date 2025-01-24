<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\CollectionMembership;

class CollectionMembershipCreated extends Event
{
    public function __construct(public CollectionMembership $data, Request $request)
    {
        parent::__construct($request);
    }
}
