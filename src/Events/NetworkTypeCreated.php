<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\NetworkType;

class NetworkTypeCreated extends Event
{
    public function __construct(public NetworkType $data, Request $request)
    {
        parent::__construct($request);
    }
}
