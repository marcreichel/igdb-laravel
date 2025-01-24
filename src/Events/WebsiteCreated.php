<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Website;

class WebsiteCreated extends Event
{
    public function __construct(public Website $data, Request $request)
    {
        parent::__construct($request);
    }
}
