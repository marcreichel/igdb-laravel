<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Cover;

class CoverCreated extends Event
{
    public function __construct(public Cover $data, Request $request)
    {
        parent::__construct($request);
    }
}
