<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\CompanyLogo;

class CompanyLogoCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var CompanyLogo
     */
    public $companyLogo;

    /**
     * @param CompanyLogo $companyLogo
     * @return void
     */
    public function __construct(CompanyLogo $companyLogo)
    {
        $this->companyLogo = $companyLogo;
    }
}
