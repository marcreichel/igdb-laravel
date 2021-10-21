<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\PlayerPerspective;

class PlayerPerspectiveCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PlayerPerspective
     */
    public $data;

    /**
     * @param PlayerPerspective $data
     *
     * @return void
     */
    public function __construct(PlayerPerspective $data)
    {
        $this->data = $data;
    }
}
