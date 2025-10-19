<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PermisoController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::post('/usuarios', [UsuarioController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);

Route::middleware(['auth:api', 'throttle:1000,1'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UsuarioController::class, 'index']);
        Route::get('/{id}', [UsuarioController::class, 'show']);
        Route::post('/', [UsuarioController::class, 'store']);
        Route::post('/admin', [UsuarioController::class, 'storeAdmin']);
        Route::put('/{id}', [UsuarioController::class, 'update']);
        Route::delete('/{id}', [UsuarioController::class, 'destroy']);

        Route::get('/{id}/permisos', [UsuarioController::class, 'permisosUsuario']);
    });

    Route::prefix('roles')->group(function () {
        Route::get('/', [RolController::class, 'index']);
        Route::get('/{id}', [RolController::class, 'show']);
        Route::post('/', [RolController::class, 'store']);
        Route::put('/{id}', [RolController::class, 'update']);
        Route::delete('/{id}', [RolController::class, 'destroy']);
    });

    Route::prefix('permisos')->group(function () {
        Route::get('/', [PermisoController::class, 'index']);
        Route::get('/{id}', [PermisoController::class, 'show']);
        Route::post('/', [PermisoController::class, 'store']);
        Route::put('/{id}', [PermisoController::class, 'update']);
        Route::delete('/{id}', [PermisoController::class, 'destroy']);

        Route::post('/usuario', [PermisoController::class, 'asignarPermisoUsuario']);
        Route::post('/rol', [PermisoController::class, 'asignarPermisoRol']);
    });
});

