<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\DateFormat;

class DateFormatCreated extends Event
{
    public function __construct(public DateFormat $data, Request $request)
    {
        parent::__construct($request);
    }
}
