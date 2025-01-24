<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\GameLocalization;

class GameLocalizationCreated extends Event
{
    public function __construct(public GameLocalization $data, Request $request)
    {
        parent::__construct($request);
    }
}
