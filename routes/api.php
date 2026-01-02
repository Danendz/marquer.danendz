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
            Route::get('/{note}', [NoteController::class, 'show'])->whereNumber('note');
            Route::post('/', [NoteController::class, 'store']);
            Route::put('/{note}', [NoteController::class, 'update'])->whereNumber('note');
            Route::delete('/{note}', [NoteController::class, 'destroy'])->whereNumber('note');
        });

        Route::prefix('tasks')->group(function () {
            Route::get('/', [TaskController::class, 'index']);
            Route::post('/', [TaskController::class, 'store']);
            Route::put('/{task}', [TaskController::class, 'update'])->whereNumber('task');
            Route::delete('/{task}', [TaskController::class, 'destroy'])->whereNumber('task');
        });

        Route::prefix('task-folders')->group(function () {
            Route::get('/', [TaskFolderController::class, 'index']);
            Route::post('/', [TaskFolderController::class, 'store']);
            Route::put('/{taskFolder}', [TaskFolderController::class, 'update'])->whereNumber('taskFolder');
            Route::delete('/{taskFolder}', [TaskFolderController::class, 'destroy'])->whereNumber('taskFolder');
        });

        Route::prefix('task-categories')->group(function () {
            Route::post('/', [TaskCategoryController::class, 'store']);
            Route::put('/{taskCategory}', [TaskCategoryController::class, 'update'])->whereNumber('taskCategory');
            Route::delete('/{taskCategory}', [TaskCategoryController::class, 'destroy'])->whereNumber('taskCategory');
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
