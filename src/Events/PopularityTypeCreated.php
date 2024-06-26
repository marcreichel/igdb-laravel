<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\PopularityType;

class PopularityTypeCreated extends Event
{
    public PopularityType $data;

    public function __construct(PopularityType $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
