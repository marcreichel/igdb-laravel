<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\PlatformFamily;

class PlatformFamilyCreated extends Event
{
    public PlatformFamily $data;

    public function __construct(PlatformFamily $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
