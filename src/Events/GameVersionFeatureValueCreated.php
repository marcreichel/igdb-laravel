<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\GameVersionFeatureValue;

class GameVersionFeatureValueCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var GameVersionFeatureValue
     */
    public $gameVersionFeatureValue;

    /**
     * @param GameVersionFeatureValue $gameVersionFeatureValue
     * @return void
     */
    public function __construct(GameVersionFeatureValue $gameVersionFeatureValue)
    {
        $this->gameVersionFeatureValue = $gameVersionFeatureValue;
    }
}
