<?php

use App\Models\Activity;
use App\Models\NewsItem;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => config('app.name'),
        'service' => 'Global F.O.R API',
        'status' => 'ok',
    ]);
});

Route::middleware(['auth', 'active.admin'])
    ->prefix('studio')
    ->name('studio.')
    ->group(function () {
        Route::redirect('/', '/studio/activities')->name('home');

        Route::view('/activities', 'studio.activities.index')->name('activities.index');
        Route::view('/activities/create', 'studio.activities.create')->name('activities.create');
        Route::get('/activities/{activity}/edit', fn (Activity $activity) => view('studio.activities.edit', compact('activity')))
            ->name('activities.edit');

        Route::view('/news', 'studio.news.index')->name('news.index');
        Route::view('/news/create', 'studio.news.create')->name('news.create');
        Route::get('/news/{newsItem}/edit', fn (NewsItem $newsItem) => view('studio.news.edit', compact('newsItem')))
            ->name('news.edit');

        Route::view('/account', 'studio.account')->name('account');
        Route::view('/settings', 'studio.settings')->name('settings');
        Route::view('/administrators', 'studio.administrators')->name('administrators.index');
    });
