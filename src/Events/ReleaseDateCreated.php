<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\ReleaseDate;

class ReleaseDateCreated extends Event
{
    public ReleaseDate $data;

    public function __construct(ReleaseDate $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
