<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\GameVersion;

class GameVersionCreated extends Event
{
    public function __construct(public GameVersion $data, Request $request)
    {
        parent::__construct($request);
    }
}
