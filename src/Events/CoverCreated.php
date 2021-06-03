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
    public $cover;

    /**
     * @param Cover $cover
     * @return void
     */
    public function __construct(Cover $cover)
    {
        $this->cover = $cover;
    }
}
