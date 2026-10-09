<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\AgeRatingContentDescriptionType;

class AgeRatingContentDescriptionTypeCreated extends Event
{
    public function __construct(public AgeRatingContentDescriptionType $data, Request $request)
    {
        parent::__construct($request);
    }
}
