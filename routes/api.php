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
Route::post('/usuarios', [UsuarioController::class, 'store']);

Route::middleware(['auth:api', 'throttle:1000,1'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->middleware('permiso:ver-usuarios');
        Route::get('/{id}', [UsuarioController::class, 'show'])->middleware('permiso:editar-usuarios');
        Route::post('/admin', [UsuarioController::class, 'storeAdmin'])->middleware('permiso:crear-usuarios');
        Route::put('/{id}', [UsuarioController::class, 'update'])->middleware('permiso:editar-usuarios');
        Route::delete('/{id}', [UsuarioController::class, 'destroy'])->middleware('permiso:eliminar-usuarios');
        Route::get('/{id}/permisos', [UsuarioController::class, 'permisosUsuario'])->middleware('permiso:permisos-usuarios');
        Route::get('/{id}/permisos-disponibles', [UsuarioController::class, 'permisosDisponibles']);
        Route::get('/permisos/mi-usuario', [UsuarioController::class, 'misPermisos']);
    });

    Route::prefix('roles')->group(function () {
        Route::get('/', [RolController::class, 'index']);
        Route::get('/{id}', [RolController::class, 'show']);
        Route::post('/', [RolController::class, 'store']);
        Route::put('/{id}', [RolController::class, 'update']);
        Route::delete('/{id}', [RolController::class, 'destroy']);
        Route::get('/{id}/permisos', [RolController::class, 'permisosRol'])->middleware('permiso:permisos-roles');
        Route::get('/{id}/permisos-disponibles', [RolController::class, 'permisosDisponibles']);
    });

    Route::prefix('permisos')->group(function () {
        Route::get('/', [PermisoController::class, 'index']);
        Route::get('/{id}', [PermisoController::class, 'show']);
        Route::post('/', [PermisoController::class, 'store']);
        Route::put('/{id}', [PermisoController::class, 'update']);
        Route::delete('/{id}', [PermisoController::class, 'destroy']);

        Route::post('/asignar', [PermisoController::class, 'asignarPermiso']);
        Route::delete('/quitar/permiso', [PermisoController::class, 'quitarPermiso']);
    });
});

