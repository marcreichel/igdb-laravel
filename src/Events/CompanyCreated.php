<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Company;

class CompanyCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Company
     */
    public $company;

    /**
     * @param Company $company
     * @return void
     */
    public function __construct(Company $company)
    {
        $this->company = $company;
    }
}
