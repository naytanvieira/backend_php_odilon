<?php

use App\Http\Controllers\AuthController;
 use App\Http\Controllers\PlanilhaProcessamentoLogController;
 use App\Http\Controllers\DashboardController;
 use App\Http\Controllers\ProfileController;
 use App\Http\Controllers\PermissionController;
 use App\Http\Controllers\SpreadsheetTypeController;
 use App\Http\Controllers\SectorController;
 use App\Http\Controllers\PatientController;
  use App\Http\Controllers\InternationController;


Route::post('user/login', [AuthController::class, 'login']);
Route::post('user/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user/user', [AuthController::class, 'me']);
    Route::get('/user/show/{id}', [AuthController::class, 'show']);
    Route::get('/user/busca', [AuthController::class, 'queryAll']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/user/update/{id}', [AuthController::class, 'update']);
    Route::put('/user/deactivate/{id}', [AuthController::class, 'deactivate']);
    Route::put('/user/update-me', [AuthController::class, 'updateMe']);

    Route::get('/dashboard/metrics', [DashboardController::class, 'metrics']);
     Route::get('/dashboard/resumo', [DashboardController::class, 'resumo']);

   

    Route::prefix('planilhas/logs')->group(function () {
        Route::get('/', [PlanilhaProcessamentoLogController::class, 'index']);
        Route::post('/', [PlanilhaProcessamentoLogController::class, 'store']);
    });

    // routes/api.php

    Route::prefix('profiles')->group(function () {
        Route::get('/', [ProfileController::class, 'index']);
        Route::post('/', [ProfileController::class, 'store']);
        Route::get('/{id}', [ProfileController::class, 'show']);
        Route::put('/{id}', [ProfileController::class, 'update']);
        Route::delete('/{id}', [ProfileController::class, 'destroy']);
    });


    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index']);
        Route::post('/', [PermissionController::class, 'store']);
        Route::get('/{id}', [PermissionController::class, 'show']);
        Route::put('/{id}', [PermissionController::class, 'update']);
        Route::delete('/{id}', [PermissionController::class, 'destroy']);
    });

    

    Route::prefix('spreadsheet-types')->group(function () {

        Route::get('/', [SpreadsheetTypeController::class, 'index']);

        Route::get('/show/{id}', [SpreadsheetTypeController::class, 'show']);

        Route::post('/store', [SpreadsheetTypeController::class, 'store']);

        Route::put('/update/{id}', [SpreadsheetTypeController::class, 'update']);

        Route::delete('/delete/{id}', [SpreadsheetTypeController::class, 'destroy']);
    });


    Route::prefix('sectors')->group(function () {

        Route::get('/', [SectorController::class, 'index']);

        Route::get('/show/{id}', [SectorController::class, 'show']);

        Route::post('/store', [SectorController::class, 'store']);

        Route::put('/update/{id}', [SectorController::class, 'update']);

        Route::delete('/delete/{id}', [SectorController::class, 'destroy']);
    });

    Route::post('/patients/import',[PatientController::class, 'import']);
    Route::get('/patients',[PatientController::class, 'queryAll']);

   

Route::prefix('internations')->group(function () {
    Route::get('/exportar', [InternationController::class, 'exportar']);
    Route::get('/', [InternationController::class, 'index']);
    Route::post('/', [InternationController::class, 'store']);
    Route::get('/{id}', [InternationController::class, 'show']);
    Route::put('/{id}', [InternationController::class, 'update']);
    Route::delete('/{id}', [InternationController::class, 'destroy']);
    
});
});

Route::get(
    '/dashboard/tempo-economizado',
    [PlanilhaProcessamentoLogController::class, 'tempoEconomizado']
);