<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\GameMode;

class GameModeCreated extends Event
{
    public function __construct(public GameMode $data, Request $request)
    {
        parent::__construct($request);
    }
}
