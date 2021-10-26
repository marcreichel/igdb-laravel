<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Franchise;

class FranchiseCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Franchise
     */
    public Franchise $data;

    /**
     * @param Franchise $data
     * @param Request   $request
     */
    public function __construct(Franchise $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
