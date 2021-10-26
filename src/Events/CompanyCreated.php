<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Company;

class CompanyCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Company
     */
    public Company $data;

    /**
     * @param Company $data
     * @param Request $request
     */
    public function __construct(Company $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
