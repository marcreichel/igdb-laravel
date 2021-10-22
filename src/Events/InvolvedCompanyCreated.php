<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\InvolvedCompany;

class InvolvedCompanyCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var InvolvedCompany
     */
    public InvolvedCompany $data;

    /**
     * @param InvolvedCompany $data
     *
     * @return void
     */
    public function __construct(InvolvedCompany $data)
    {
        $this->data = $data;
    }
}
