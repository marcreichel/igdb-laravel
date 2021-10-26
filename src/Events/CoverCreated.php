<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Cover;

class CoverCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Cover
     */
    public Cover $data;

    /**
     * @param Cover   $data
     * @param Request $request
     */
    public function __construct(Cover $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
