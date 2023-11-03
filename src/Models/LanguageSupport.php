<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class LanguageSupport extends Model
{
    protected array $casts = [
        'game' => Game::class,
        'language' => Language::class,
        'language_support_type' => LanguageSupportType::class,
    ];
}
