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
    public $data;

    /**
     * @param Collection $data
     *
     * @return void
     */
    public function __construct(Collection $data)
    {
        $this->data = $data;
    }
}
