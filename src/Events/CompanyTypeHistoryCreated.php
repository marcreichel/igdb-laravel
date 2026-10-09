<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\CompanyTypeHistory;

class CompanyTypeHistoryCreated extends Event
{
    public function __construct(public CompanyTypeHistory $data, Request $request)
    {
        parent::__construct($request);
    }
}
