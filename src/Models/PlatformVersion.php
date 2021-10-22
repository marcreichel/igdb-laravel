<?php

namespace MarcReichel\IGDBLaravel\Models;

class PlatformVersion extends Model
{
    protected array $casts = [
        'companies' => PlatformVersionCompany::class,
        'main_manufacturer' => PlatformVersionCompany::class,
    ];
}
