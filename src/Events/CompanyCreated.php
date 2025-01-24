<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Company;

class CompanyCreated extends Event
{
    public function __construct(public Company $data, Request $request)
    {
        parent::__construct($request);
    }
}
