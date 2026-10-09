<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\AgeRatingContentDescriptionV2;

class AgeRatingContentDescriptionV2Created extends Event
{
    public function __construct(public AgeRatingContentDescriptionV2 $data, Request $request)
    {
        parent::__construct($request);
    }
}
