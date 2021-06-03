<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Collection;

class CollectionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Collection
     */
    public $collection;

    /**
     * @param Collection $collection
     * @return void
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }
}
