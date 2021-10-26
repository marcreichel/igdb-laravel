<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Keyword;

class KeywordCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Keyword
     */
    public Keyword $data;

    /**
     * @param Keyword $data
     * @param Request $request
     */
    public function __construct(Keyword $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
