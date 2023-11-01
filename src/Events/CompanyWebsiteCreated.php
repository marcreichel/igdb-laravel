<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\CompanyWebsite;

class CompanyWebsiteCreated extends Event
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public CompanyWebsite $data;

    public function __construct(CompanyWebsite $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
