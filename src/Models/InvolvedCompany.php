<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class InvolvedCompany extends Model
{
    protected array $casts = [
        'company' => Company::class,
        'game' => Game::class,
    ];
}
