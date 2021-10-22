<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\CompanyWebsite;

class CompanyWebsiteCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var CompanyWebsite
     */
    public CompanyWebsite $data;

    /**
     * @param CompanyWebsite $data
     *
     * @return void
     */
    public function __construct(CompanyWebsite $data)
    {
        $this->data = $data;
    }
}
