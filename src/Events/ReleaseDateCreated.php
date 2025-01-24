<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\ReleaseDate;

class ReleaseDateCreated extends Event
{
    public function __construct(public ReleaseDate $data, Request $request)
    {
        parent::__construct($request);
    }
}
