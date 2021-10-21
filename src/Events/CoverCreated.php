<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Cover;

class CoverCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Cover
     */
    public $data;

    /**
     * @param Cover $data
     *
     * @return void
     */
    public function __construct(Cover $data)
    {
        $this->data = $data;
    }
}
