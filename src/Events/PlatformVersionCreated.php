<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\PlatformVersion;

class PlatformVersionCreated extends Event
{
    public PlatformVersion $data;

    public function __construct(PlatformVersion $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
