<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Website;

class WebsiteCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Website
     */
    public Website $data;

    /**
     * @param Website $data
     * @param Request $request
     */
    public function __construct(Website $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
