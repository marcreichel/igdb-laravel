<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\GameVideo;

class GameVideoCreated extends Event
{
    public function __construct(public GameVideo $data, Request $request)
    {
        parent::__construct($request);
    }
}
