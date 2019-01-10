<?php

namespace MarcReichel\IGDBLaravel\Models;


class ListModel extends Model
{
    public $privateEndpoint = true;
    protected $endpoint = 'private/lists';

    protected $casts = [
        'similar_lists' => self::class,
    ];
}
