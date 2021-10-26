<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\GameVersion;

class GameVersionCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var GameVersion
     */
    public GameVersion $data;

    /**
     * @param GameVersion $data
     * @param Request     $request
     */
    public function __construct(GameVersion $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
