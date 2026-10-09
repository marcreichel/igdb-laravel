<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\AgeRatingCategory;

class AgeRatingCategoryCreated extends Event
{
    public function __construct(public AgeRatingCategory $data, Request $request)
    {
        parent::__construct($request);
    }
}
