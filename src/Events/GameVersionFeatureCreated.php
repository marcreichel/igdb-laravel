<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\GameVersionFeature;

class GameVersionFeatureCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var GameVersionFeature
     */
    public GameVersionFeature $data;

    /**
     * @param GameVersionFeature $data
     * @param Request            $request
     */
    public function __construct(GameVersionFeature $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
