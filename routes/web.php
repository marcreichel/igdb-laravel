<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use MarcReichel\IGDBLaravel\Models\Webhook;

Route::post('{model}/{method}', function (Request $request) {
    return Webhook::handle($request);
})->name('handle-igdb-webhook');
