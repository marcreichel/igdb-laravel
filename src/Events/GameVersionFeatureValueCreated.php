<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\GameVersionFeatureValue;

class GameVersionFeatureValueCreated extends Event
{
    public function __construct(public GameVersionFeatureValue $data, Request $request)
    {
        parent::__construct($request);
    }
}
