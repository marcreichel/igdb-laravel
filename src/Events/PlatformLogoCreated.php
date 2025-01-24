<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\PlatformLogo;

class PlatformLogoCreated extends Event
{
    public function __construct(public PlatformLogo $data, Request $request)
    {
        parent::__construct($request);
    }
}
