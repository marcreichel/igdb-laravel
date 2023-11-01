<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Carbon\Carbon;
use Illuminate\Http\Request;

abstract class Event
{
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
