<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\WebsiteType;

class WebsiteTypeCreated extends Event
{
    public function __construct(public WebsiteType $data, Request $request)
    {
        parent::__construct($request);
    }
}
