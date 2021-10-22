<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Keyword;

class KeywordCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Keyword
     */
    public Keyword $data;

    /**
     * @param Keyword $data
     *
     * @return void
     */
    public function __construct(Keyword $data)
    {
        $this->data = $data;
    }
}
