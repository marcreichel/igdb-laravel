<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\InvolvedCompany;

class InvolvedCompanyCreated extends Event
{
    public function __construct(public InvolvedCompany $data, Request $request)
    {
        parent::__construct($request);
    }
}
