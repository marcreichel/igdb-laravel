<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\NetworkType;

class NetworkTypeCreated extends Event
{
    public NetworkType $data;

    public function __construct(NetworkType $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
