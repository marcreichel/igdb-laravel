<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Executable;

class ExecutableCreated extends Event
{
    public function __construct(public Executable $data, Request $request)
    {
        parent::__construct($request);
    }
}
