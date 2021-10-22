<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\CharacterMugShot;

class CharacterMugShotCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var CharacterMugShot
     */
    public CharacterMugShot $data;

    /**
     * @param CharacterMugShot $data
     *
     * @return void
     */
    public function __construct(CharacterMugShot $data)
    {
        $this->data = $data;
    }
}
