<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\CompanyWebsite;

class CompanyWebsiteCreated extends Event
{
    public CompanyWebsite $data;

    public function __construct(CompanyWebsite $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
