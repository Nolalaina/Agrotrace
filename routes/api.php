<?php

use App\Http\Controllers\Api\LotController;
use App\Http\Controllers\Api\RecolteController;
use App\Http\Controllers\Api\TokenController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [TokenController::class, 'store'])->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/lots', [LotController::class, 'index']);
    Route::get('/lots/{code}', [LotController::class, 'show']);
    Route::get('/lots/{code}/tracabilite', [LotController::class, 'tracabilite']);

    Route::get('/recoltes', [RecolteController::class, 'index']);
    Route::post('/recoltes', [RecolteController::class, 'store']);
    Route::patch('/recoltes/{recolte}', [RecolteController::class, 'update']);
});
