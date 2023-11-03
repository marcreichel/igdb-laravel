<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\AgeRatingContentDescription;

class AgeRatingContentDescriptionCreated extends Event
{
    public AgeRatingContentDescription $data;

    public function __construct(AgeRatingContentDescription $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
