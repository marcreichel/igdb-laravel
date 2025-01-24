<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\AgeRating;

class AgeRatingCreated extends Event
{
    public function __construct(public AgeRating $data, Request $request)
    {
        parent::__construct($request);
    }
}
