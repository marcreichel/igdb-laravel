<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class Company extends Model
{
    protected array $casts = [
        'change_date_format' => DateFormat::class,
        'changed_company_id' => self::class,
        'developed' => Game::class,
        'logo' => CompanyLogo::class,
        'parent' => self::class,
        'published' => Game::class,
        'start_date_format' => DateFormat::class,
        'websites' => CompanyWebsite::class,
    ];
}
