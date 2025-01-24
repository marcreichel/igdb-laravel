<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\ExternalGame;

class ExternalGameCreated extends Event
{
    public function __construct(public ExternalGame $data, Request $request)
    {
        parent::__construct($request);
    }
}
