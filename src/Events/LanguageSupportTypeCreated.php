<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\LanguageSupportType;

class LanguageSupportTypeCreated extends Event
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public LanguageSupportType $data;

    public function __construct(LanguageSupportType $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
