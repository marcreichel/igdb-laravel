<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Franchise;

class FranchiseCreated extends Event
{
    public Franchise $data;

    public function __construct(Franchise $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
