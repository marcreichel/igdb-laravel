<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\GameVersionFeatureValue;

class GameVersionFeatureValueCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var GameVersionFeatureValue
     */
    public GameVersionFeatureValue $data;

    /**
     * @param GameVersionFeatureValue $data
     * @param Request                 $request
     */
    public function __construct(GameVersionFeatureValue $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
