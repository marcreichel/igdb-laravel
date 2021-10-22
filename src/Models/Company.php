<?php

namespace MarcReichel\IGDBLaravel\Models;

class Company extends Model
{
    protected array $casts = [
        'changed_company_id' => self::class,
        'developed' => Game::class,
        'parent' => self::class,
        'published' => Game::class,
        'websites' => CompanyWebsite::class,
    ];
}
