<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\CompanyLogo;

class CompanyLogoCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var CompanyLogo
     */
    public CompanyLogo $data;

    /**
     * @param CompanyLogo $data
     * @param Request     $request
     */
    public function __construct(CompanyLogo $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
