<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\ImageType;

class ImageTypeCreated extends Event
{
    public function __construct(public ImageType $data, Request $request)
    {
        parent::__construct($request);
    }
}
