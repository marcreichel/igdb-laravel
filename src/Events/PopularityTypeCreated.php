<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\PopularityType;

class PopularityTypeCreated extends Event
{
    public function __construct(public PopularityType $data, Request $request)
    {
        parent::__construct($request);
    }
}
