<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Screenshot;

class ScreenshotCreated extends Event
{
    public Screenshot $data;

    public function __construct(Screenshot $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
