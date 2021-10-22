<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Website;

class WebsiteCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Website
     */
    public Website $data;

    /**
     * @param Website $data
     *
     * @return void
     */
    public function __construct(Website $data)
    {
        $this->data = $data;
    }
}
