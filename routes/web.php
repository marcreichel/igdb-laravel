<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use MarcReichel\IGDBLaravel\Models\Webhook;

/**
 * @deprecated
 */
Route::post('{model}/{method}', function (Request $request) {
    return Webhook::handle($request);
});

Route::post(substr(md5(config('igdb.credentials.client_id')), 0, 8) . '/{model}/{method}', function (Request $request) {
    return Webhook::handle($request);
})->name('handle-igdb-webhook');
