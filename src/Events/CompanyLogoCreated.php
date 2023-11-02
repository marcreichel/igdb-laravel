<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\CompanyLogo;

class CompanyLogoCreated extends Event
{
    public CompanyLogo $data;

    public function __construct(CompanyLogo $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
