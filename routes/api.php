<?php

declare(strict_types=1);

use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/articles')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/{id}', [ArticleController::class, 'show'])->name('articles.show')->where('id', '[0-9]+');
});


Route::group(['prefix' => 'v1/'], function () {
    Route::get('/', function () {
        return "API is working";
    });
});
