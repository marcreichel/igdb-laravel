<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Franchise;

class FranchiseCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Franchise
     */
    public Franchise $data;

    /**
     * @param Franchise $data
     *
     * @return void
     */
    public function __construct(Franchise $data)
    {
        $this->data = $data;
    }
}
