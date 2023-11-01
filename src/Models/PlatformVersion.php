<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class PlatformVersion extends Model
{
    protected array $casts = [
        'companies' => PlatformVersionCompany::class,
        'main_manufacturer' => PlatformVersionCompany::class,
        'platform_logo' => PlatformLogo::class,
        'platform_version_release_dates' => PlatformVersionReleaseDate::class,
    ];
}
