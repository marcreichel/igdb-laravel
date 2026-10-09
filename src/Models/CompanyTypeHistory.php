<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class CompanyTypeHistory extends Model
{
    protected array $casts = [
        'parent_company' => Company::class,
    ];
}
