<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Screenshot;

class ScreenshotCreated extends Event
{
    public function __construct(public Screenshot $data, Request $request)
    {
        parent::__construct($request);
    }
}
