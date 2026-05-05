<?php

use App\Http\Controllers\AuthController;
 use App\Http\Controllers\PlanilhaProcessamentoLogController;
 use App\Http\Controllers\DashboardController;

Route::post('user/login', [AuthController::class, 'login']);
Route::post('user/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user/user', [AuthController::class, 'me']);
    Route::get('/user/show/{id}', [AuthController::class, 'show']);
    Route::get('/user/busca', [AuthController::class, 'queryAll']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/user/update/{id}', [AuthController::class, 'update']);
    Route::put('/user/deactivate/{id}', [AuthController::class, 'deactivate']);

    Route::get('/dashboard/metrics', [DashboardController::class, 'metrics']);

   

    Route::prefix('planilhas/logs')->group(function () {
        Route::get('/', [PlanilhaProcessamentoLogController::class, 'index']);
        Route::post('/', [PlanilhaProcessamentoLogController::class, 'store']);
    });
});