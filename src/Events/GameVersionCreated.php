<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\GameVersion;

class GameVersionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var GameVersion
     */
    public $data;

    /**
     * @param GameVersion $data
     *
     * @return void
     */
    public function __construct(GameVersion $data)
    {
        $this->data = $data;
    }
}
