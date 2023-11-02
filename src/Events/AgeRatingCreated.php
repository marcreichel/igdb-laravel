<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\AgeRating;

class AgeRatingCreated extends Event
{
    public AgeRating $data;

    public function __construct(AgeRating $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
