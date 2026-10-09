<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\GameType;

class GameTypeCreated extends Event
{
    public function __construct(public GameType $data, Request $request)
    {
        parent::__construct($request);
    }
}
