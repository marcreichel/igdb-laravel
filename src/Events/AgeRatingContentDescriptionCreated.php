<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\AgeRatingContentDescription;

class AgeRatingContentDescriptionCreated extends Event
{
    public function __construct(public AgeRatingContentDescription $data, Request $request)
    {
        parent::__construct($request);
    }
}
