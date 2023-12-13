<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Interfaces;

interface ModelInterface
{
    public function __construct(array $properties = []);
}
