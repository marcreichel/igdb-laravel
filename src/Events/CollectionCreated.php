<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Collection;

class CollectionCreated extends Event
{
    public function __construct(public Collection $data, Request $request)
    {
        parent::__construct($request);
    }
}
