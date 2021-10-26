<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\PlayerPerspective;

class PlayerPerspectiveCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PlayerPerspective
     */
    public PlayerPerspective $data;

    /**
     * @param PlayerPerspective $data
     * @param Request           $request
     */
    public function __construct(PlayerPerspective $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
