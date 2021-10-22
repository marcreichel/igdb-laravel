<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\GameVersionFeature;

class GameVersionFeatureCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var GameVersionFeature
     */
    public GameVersionFeature $data;

    /**
     * @param GameVersionFeature $data
     *
     * @return void
     */
    public function __construct(GameVersionFeature $data)
    {
        $this->data = $data;
    }
}
