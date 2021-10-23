<?php

namespace MarcReichel\IGDBLaravel\Interfaces;

interface ModelInterface
{
    /**
     * @param  array  $properties
     */
    public function __construct(array $properties = []);
}
