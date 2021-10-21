<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\AlternativeName;

class AlternativeNameCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var AlternativeName
     */
    public $data;

    /**
     * @param AlternativeName $data
     *
     * @return void
     */
    public function __construct(AlternativeName $data)
    {
        $this->data = $data;
    }
}
