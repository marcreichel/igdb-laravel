<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class Company extends Model
{
    protected array $casts = [
        'changed_company_id' => self::class,
        'developed' => Game::class,
        'logo' => CompanyLogo::class,
        'parent' => self::class,
        'published' => Game::class,
        'websites' => CompanyWebsite::class,
    ];
}
