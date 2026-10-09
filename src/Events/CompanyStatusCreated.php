<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\CompanyStatus;

class CompanyStatusCreated extends Event
{
    public function __construct(public CompanyStatus $data, Request $request)
    {
        parent::__construct($request);
    }
}
