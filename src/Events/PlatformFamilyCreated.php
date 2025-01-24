<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\PlatformFamily;

class PlatformFamilyCreated extends Event
{
    public function __construct(public PlatformFamily $data, Request $request)
    {
        parent::__construct($request);
    }
}
