<?php

use App\Http\Controllers\Admin\Util\RankController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::put('/models/update-rank', [RankController::class, 'updateRank'])->name('models.rank.update');

