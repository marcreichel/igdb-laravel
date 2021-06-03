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
    public $website;

    /**
     * @param Website $website
     * @return void
     */
    public function __construct(Website $website)
    {
        $this->website = $website;
    }
}
