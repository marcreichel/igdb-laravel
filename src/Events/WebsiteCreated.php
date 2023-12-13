<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Website;

class WebsiteCreated extends Event
{
    public Website $data;

    public function __construct(Website $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
