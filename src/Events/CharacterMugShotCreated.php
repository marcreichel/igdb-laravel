<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\CharacterMugShot;

class CharacterMugShotCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var CharacterMugShot
     */
    public CharacterMugShot $data;

    /**
     * @param CharacterMugShot $data
     * @param Request          $request
     */
    public function __construct(CharacterMugShot $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
