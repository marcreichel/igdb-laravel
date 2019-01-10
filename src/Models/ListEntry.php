<?php

namespace MarcReichel\IGDBLaravel\Models;


class ListEntry extends Model
{
    public $privateEndpoint = true;

    protected $casts = [
        'list' => ListModel::class,
    ];
}
