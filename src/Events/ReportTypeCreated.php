<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\ReportType;

class ReportTypeCreated extends Event
{
    public function __construct(public ReportType $data, Request $request)
    {
        parent::__construct($request);
    }
}
