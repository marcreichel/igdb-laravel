<?php

namespace MarcReichel\IGDBLaravel\Events;

use Carbon\Carbon;
use Illuminate\Http\Request;

abstract class Event
{
    /**
     * @var string $class
     */
    public string $class;

    /**
     * @var string $url
     */
    public string $url;

    /**
     * @var string|null $method
     */
    public string|null $method;

    /**
     * @var Carbon $created_at
     */
    public Carbon $created_at;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->class = get_class($this);
        $this->url = $request->fullUrl();
        $this->method = (string) $request->route('method');
        $this->created_at = new Carbon();
    }
}
