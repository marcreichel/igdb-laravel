<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\CompanySize;

class CompanySizeCreated extends Event
{
    public function __construct(public CompanySize $data, Request $request)
    {
        parent::__construct($request);
    }
}
