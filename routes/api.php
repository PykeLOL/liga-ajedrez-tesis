<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LigaController;
use App\Http\Controllers\ClubController;
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
        Route::get('/', [RolController::class, 'index'])->middleware('permiso:ver-roles');
        Route::get('/{id}', [RolController::class, 'show'])->middleware('permiso:editar-roles');
        Route::post('/', [RolController::class, 'store'])->middleware('permiso:crear-roles');
        Route::put('/{id}', [RolController::class, 'update'])->middleware('permiso:editar-roles');
        Route::delete('/{id}', [RolController::class, 'destroy'])->middleware('permiso:eliminar-roles');
        Route::get('/{id}/permisos', [RolController::class, 'permisosRol'])->middleware('permiso:permisos-roles');
        Route::get('/{id}/permisos-disponibles', [RolController::class, 'permisosDisponibles'])->middleware('permiso:permisos-roles');
    });

    Route::prefix('permisos')->group(function () {
        Route::get('/', [PermisoController::class, 'index'])->middleware('permiso:ver-permisos');
        Route::get('/{id}', [PermisoController::class, 'show'])->middleware('permiso:editar-permisos');
        Route::post('/', [PermisoController::class, 'store'])->middleware('permiso:crear-permisos');
        Route::put('/{id}', [PermisoController::class, 'update'])->middleware('permiso:editar-permisos');
        Route::delete('/{id}', [PermisoController::class, 'destroy'])->middleware('permiso:eliminar-permisos');

        Route::post('/asignar', [PermisoController::class, 'asignarPermiso'])->middleware('permiso:permisos-usuarios|permisos-roles');
        Route::delete('/quitar/permiso', [PermisoController::class, 'quitarPermiso'])->middleware('permiso:permisos-usuarios|permisos-roles');
    });

    Route::prefix('ligas')->group(function () {
        Route::get('/', [LigaController::class, 'index'])->middleware('permiso:ver-ligas');
        Route::post('/', [LigaController::class, 'store'])->middleware('permiso:crear-ligas');
        Route::get('/{id}', [LigaController::class, 'show'])->middleware('permiso:editar-ligas');
        Route::put('/{id}', [LigaController::class, 'update'])->middleware('permiso:editar-ligas');
        Route::delete('/{id}', [LigaController::class, 'destroy'])->middleware('permiso:eliminar-ligas');
    });

    Route::prefix('clubes')->group(function () {
        Route::get('/', [ClubController::class, 'index'])->middleware('permiso:ver-clubes');
        Route::post('/', [ClubController::class, 'store'])->middleware('permiso:crear-clubes');
        Route::get('/{id}', [ClubController::class, 'show'])->middleware('permiso:editar-clubes');
        Route::put('/{id}', [ClubController::class, 'update'])->middleware('permiso:editar-clubes');
        Route::delete('/{id}', [ClubController::class, 'destroy'])->middleware('permiso:eliminar-clubes');
    });
});

