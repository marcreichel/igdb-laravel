<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Character;

class CharacterCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Character
     */
    public $data;

    /**
     * @param Character $data
     *
     * @return void
     */
    public function __construct(Character $data)
    {
        $this->data = $data;
    }
}
