<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\CompanyWebsite;

class CompanyWebsiteCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var CompanyWebsite
     */
    public CompanyWebsite $data;

    /**
     * @param CompanyWebsite $data
     * @param Request        $request
     */
    public function __construct(CompanyWebsite $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
