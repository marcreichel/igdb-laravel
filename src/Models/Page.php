<?php

namespace MarcReichel\IGDBLaravel\Models;


class Page extends Model
{
    protected $casts = [
        'websites' => PageWebsite::class,
    ];
}
