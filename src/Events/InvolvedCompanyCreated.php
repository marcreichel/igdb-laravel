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
    public $involvedCompany;

    /**
     * @param InvolvedCompany $involvedCompany
     * @return void
     */
    public function __construct(InvolvedCompany $involvedCompany)
    {
        $this->involvedCompany = $involvedCompany;
    }
}
