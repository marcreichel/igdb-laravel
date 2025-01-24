<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Region;

class RegionCreated extends Event
{
    public function __construct(public Region $data, Request $request)
    {
        parent::__construct($request);
    }
}
