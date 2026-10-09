<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\ExternalGameSource;

class ExternalGameSourceCreated extends Event
{
    public function __construct(public ExternalGameSource $data, Request $request)
    {
        parent::__construct($request);
    }
}
