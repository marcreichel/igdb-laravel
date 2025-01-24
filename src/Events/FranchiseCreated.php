<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Franchise;

class FranchiseCreated extends Event
{
    public function __construct(public Franchise $data, Request $request)
    {
        parent::__construct($request);
    }
}
