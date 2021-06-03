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
    public $companyWebsite;

    /**
     * @param CompanyWebsite $companyWebsite
     * @return void
     */
    public function __construct(CompanyWebsite $companyWebsite)
    {
        $this->companyWebsite = $companyWebsite;
    }
}
