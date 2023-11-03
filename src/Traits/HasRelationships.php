<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Traits;

use Illuminate\Support\Collection;

trait HasRelationships
{
    public Collection $relations;
}
