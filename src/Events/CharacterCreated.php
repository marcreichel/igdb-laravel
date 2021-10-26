<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Character;

class CharacterCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Character
     */
    public Character $data;

    /**
     * @param Character $data
     * @param Request   $request
     */
    public function __construct(Character $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
