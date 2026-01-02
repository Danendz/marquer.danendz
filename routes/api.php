<?php

use App\Http\Controllers\Internal\AppReleaseIngestController;
use App\Http\Controllers\Private\NoteController;
use App\Http\Controllers\Private\Tasks\TaskCategoryController;
use App\Http\Controllers\Private\Tasks\TaskController;
use App\Http\Controllers\Private\Tasks\TaskFolderController;
use App\Http\Controllers\Public\AppReleaseController;
use App\Http\Controllers\Public\WishController;
use Illuminate\Support\Facades\Route;

// Private
Route::middleware('auth:api')->group(function () {
    Route::prefix('marquer')->group(function () {
        Route::prefix('notes')->group(function () {
            Route::get('/', [NoteController::class, 'index']);
            Route::get('/{id}', [NoteController::class, 'show'])->whereNumber('id');
            Route::post('/', [NoteController::class, 'store']);
            Route::put('/{id}', [NoteController::class, 'update'])->whereNumber('id');
            Route::delete('/{id}', [NoteController::class, 'destroy'])->whereNumber('id');
        });

        Route::prefix('tasks')->group(function () {
            Route::get('/', [TaskController::class, 'index']);
            Route::post('/', [TaskController::class, 'store']);
            Route::put('/{id}', [TaskController::class, 'update'])->whereNumber('id');
            Route::delete('/{id}', [TaskController::class, 'destroy'])->whereNumber('id');
        });

        Route::prefix('task-folders')->group(function () {
            Route::get('/', [TaskFolderController::class, 'index']);
            Route::post('/', [TaskFolderController::class, 'store']);
            Route::put('/{id}', [TaskFolderController::class, 'update'])->whereNumber('id');
            Route::delete('/{id}', [TaskFolderController::class, 'destroy'])->whereNumber('id');
        });

        Route::prefix('task-categories')->group(function () {
            Route::post('/', [TaskCategoryController::class, 'store'])->whereNumber('id');
            Route::put('/{id}', [TaskCategoryController::class, 'update'])->whereNumber('id');
            Route::delete('/{id}', [TaskCategoryController::class, 'destroy'])->whereNumber('id');
        });
    });
});

// Public
Route::prefix('marquer')->group(function () {
    Route::post('/internal/app-releases', [AppReleaseIngestController::class, 'store'])->middleware('github.oidc');
    Route::prefix('app')->group(function () {
        Route::get('/latest', [AppReleaseController::class, 'latest']);
        Route::get('/latest/download', [AppReleaseController::class, 'downloadLatest']);
    });

    Route::prefix('wish')->group(function () {
        Route::post('/', [WishController::class, 'store']);
        Route::get('/my', [WishController::class, 'index']);
        Route::get('/random', [WishController::class, 'random']);
    });
});
