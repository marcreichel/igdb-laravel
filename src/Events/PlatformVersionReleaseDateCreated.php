<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\PlatformVersionReleaseDate;

class PlatformVersionReleaseDateCreated extends Event
{
    public PlatformVersionReleaseDate $data;

    public function __construct(PlatformVersionReleaseDate $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
