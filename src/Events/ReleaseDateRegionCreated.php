<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\ReleaseDateRegion;

class ReleaseDateRegionCreated extends Event
{
    public function __construct(public ReleaseDateRegion $data, Request $request)
    {
        parent::__construct($request);
    }
}
