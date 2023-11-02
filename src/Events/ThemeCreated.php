<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Theme;

class ThemeCreated extends Event
{
    public Theme $data;

    public function __construct(Theme $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
