<?php

use App\Http\Controllers\Api\V1\ActivityController;
use App\Http\Controllers\Api\V1\NewsController;
use App\Http\Controllers\Api\V1\SiteSettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/status', fn () => [
        'name' => config('app.name'),
        'status' => 'ok',
    ]);

    Route::get('/site-settings', [SiteSettingController::class, 'show']);

    Route::get('/activities', [ActivityController::class, 'index']);
    Route::get('/news', [NewsController::class, 'index']);
    Route::get('/news/{slug}', [NewsController::class, 'show']);
});
