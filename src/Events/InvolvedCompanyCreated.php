<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\InvolvedCompany;

class InvolvedCompanyCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var InvolvedCompany
     */
    public InvolvedCompany $data;

    /**
     * @param InvolvedCompany $data
     * @param Request         $request
     */
    public function __construct(InvolvedCompany $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
