<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

abstract class Event
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public string $class;
    public string $url;
    public ?string $method;
    public Carbon $created_at;

    public function __construct(Request $request)
    {
        $this->class = static::class;
        $this->url = $request->fullUrl();
        /** @var string $method */
        $method = $request->route('method');
        $this->method = $method;
        $this->created_at = new Carbon();
    }
}
