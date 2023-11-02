<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\PlatformVersionCompany;

class PlatformVersionCompanyCreated extends Event
{
    public PlatformVersionCompany $data;

    public function __construct(PlatformVersionCompany $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
