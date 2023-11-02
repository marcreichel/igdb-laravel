<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\PlatformWebsite;

class PlatformWebsiteCreated extends Event
{
    public PlatformWebsite $data;

    public function __construct(PlatformWebsite $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
