<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\ReleaseDateStatus;

class ReleaseDateStatusCreated extends Event
{
    public function __construct(public ReleaseDateStatus $data, Request $request)
    {
        parent::__construct($request);
    }
}
