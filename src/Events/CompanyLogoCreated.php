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
    public CompanyLogo $data;

    /**
     * @param CompanyLogo $data
     *
     * @return void
     */
    public function __construct(CompanyLogo $data)
    {
        $this->data = $data;
    }
}
