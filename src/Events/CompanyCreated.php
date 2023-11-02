<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Company;

class CompanyCreated extends Event
{
    public Company $data;

    public function __construct(Company $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
