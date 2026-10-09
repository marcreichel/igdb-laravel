<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\GameReleaseFormat;

class GameReleaseFormatCreated extends Event
{
    public function __construct(public GameReleaseFormat $data, Request $request)
    {
        parent::__construct($request);
    }
}
