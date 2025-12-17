<?php

use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('marquer')->group(function () {

        Route::prefix('notes') ->group(function () {
            Route::get('/', [NoteController::class, 'index']);
            Route::get('/{id}', [NoteController::class, 'show']);
            Route::post('/', [NoteController::class, 'store']);
            Route::put('/{id}', [NoteController::class, 'update']);
            Route::delete('/{id}', [NoteController::class, 'destroy']);
        });
    });
});
