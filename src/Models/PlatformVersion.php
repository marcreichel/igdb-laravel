<?php

namespace MarcReichel\IGDBLaravel\Models;

class PlatformVersion extends Model
{
    /**
     * @var array|string[]
     */
    protected array $casts = [
        'companies' => PlatformVersionCompany::class,
        'main_manufacturer' => PlatformVersionCompany::class,
    ];
}
