<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\MultiplayerMode;

class MultiplayerModeCreated extends Event
{
    public function __construct(public MultiplayerMode $data, Request $request)
    {
        parent::__construct($request);
    }
}
