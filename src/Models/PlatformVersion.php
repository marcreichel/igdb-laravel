<?php

namespace MarcReichel\IGDBLaravel\Models;


class PlatformVersion extends Model
{
    protected $casts = [
        'companies' => PlatformVersionCompany::class,
        'main_manufacturer' => PlatformVersionCompany::class,
    ];
}
