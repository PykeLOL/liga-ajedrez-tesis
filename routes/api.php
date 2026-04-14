<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LigaController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\SelectController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\ModuloController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\TorneoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DeportistaController;
use App\Http\Controllers\TipoAccionController;
use App\Http\Controllers\ChesstoolsController;
use App\Http\Controllers\EntrenadorController;
use App\Http\Controllers\EntrenamientoController;
use App\Http\Controllers\GoogleCalendarController;

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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh'])
    ->middleware('jwt.cookie');
Route::middleware(['jwt.cookie'])->get('/me', [AuthController::class, 'me']);
Route::post('/usuarios', [UsuarioController::class, 'store']);

Route::get('/google/callback', [GoogleCalendarController::class, 'handleGoogleCallback']);

Route::prefix('select')->group(function () {
    Route::get('/tipos-evento', [SelectController::class, 'tiposEvento']);
    Route::get('/tipos-identificacion', [SelectController::class, 'tiposIdentificacion']);
    Route::get('/estados-evento', [SelectController::class, 'estadosEvento']);
    Route::get('/categorias-evento', [SelectController::class, 'categoriasEvento']);
    Route::get('/ritmos-evento', [SelectController::class, 'ritmosEvento']);
    Route::get('/generos', [SelectController::class, 'generos']);
    Route::get('/generos-deportista', [SelectController::class, 'generosDeportista']);
    Route::get('/clubes', [SelectController::class, 'clubes']);
    Route::get('/ligas', [SelectController::class, 'ligas']);
    Route::get('/redesSociales', [SelectController::class, 'redesSociales']);
    Route::get('/usuarios', [SelectController::class, 'usuarios']);
    Route::get('/usuarios-deportistas', [SelectController::class, 'usuariosDeportistas']);
    Route::get('/nacionalidades', [SelectController::class, 'nacionalidades']);
    Route::get('/titulos', [SelectController::class, 'titulos']);
});

Route::prefix('home')->group(function () {
    Route::prefix('/eventos')->group(function () {
        Route::get('/', [EventoController::class, 'indexHome']);
        Route::get('/tipo/{tipo}', [EventoController::class, 'getEventosPorTipo']);
        Route::get('/{id}', [EventoController::class, 'showPublic']);
    });

    Route::prefix('/clubes')->group(function () {
        Route::get('/', [ClubController::class, 'indexHome']);
        Route::get('/{id}', [ClubController::class, 'showPublic']);
    });

    Route::prefix('/chesstools')->group(function () {
        Route::get('/{fide_id}', [ChesstoolsController::class, 'show']);
    });

    Route::prefix('/deportistas')->group(function () {
        Route::get('/', [DeportistaController::class, 'indexHome']);
        Route::get('/{id}', [DeportistaController::class, 'showPublic']);
    });
});

Route::middleware(['auth:api', 'throttle:1000,1'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('perfil')->group(function () {
        Route::get('/', [PerfilController::class, 'index']);
        Route::get('/permisos', [PerfilController::class, 'misPermisos']);
        Route::put('/cambiar-contrasena', [PerfilController::class, 'cambiarContrasena']);
        Route::put('/', [PerfilController::class, 'update']);
        Route::delete('/eliminar-foto', [PerfilController::class, 'eliminarFoto']);
    });

    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->middleware('permiso:ver-usuarios');
        Route::get('/{id}', [UsuarioController::class, 'show'])->middleware('permiso:editar-usuarios');
        Route::post('/admin', [UsuarioController::class, 'storeAdmin'])->middleware('permiso:crear-usuarios');
        Route::put('/{id}', [UsuarioController::class, 'update'])->middleware('permiso:editar-usuarios');
        Route::delete('/{id}', [UsuarioController::class, 'destroy'])->middleware('permiso:eliminar-usuarios');
        Route::get('/{id}/permisos', [UsuarioController::class, 'permisosUsuario'])->middleware('permiso:permisos-usuarios');
        Route::get('/{id}/permisos-disponibles', [UsuarioController::class, 'permisosDisponibles']);
        Route::get('/permisos/mi-usuario', [UsuarioController::class, 'misPermisos']);
        Route::get('/tipos-identificacion/select', [UsuarioController::class, 'selectTiposIdentificacion']);
    });

    Route::prefix('roles')->group(function () {
        Route::get('/select', [RolController::class, 'index']);
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
        Route::get('/select', [LigaController::class, 'index']);
        Route::get('/', [LigaController::class, 'index'])->middleware('permiso:ver-ligas');
        Route::post('/', [LigaController::class, 'store'])->middleware('permiso:crear-ligas');
        Route::get('/{id}', [LigaController::class, 'show'])->middleware('permiso:editar-ligas');
        Route::put('/{id}', [LigaController::class, 'update'])->middleware('permiso:editar-ligas');
        Route::delete('/{id}', [LigaController::class, 'destroy'])->middleware('permiso:eliminar-ligas');
    });

    Route::prefix('clubes')->group(function () {
        Route::get('/select', [ClubController::class, 'index']);
        Route::get('/', [ClubController::class, 'index'])->middleware('permiso:ver-clubes');
        Route::post('/', [ClubController::class, 'store'])->middleware('permiso:crear-clubes');
        Route::get('/{id}', [ClubController::class, 'show'])->middleware('permiso:editar-clubes');
        Route::put('/{id}', [ClubController::class, 'update'])->middleware('permiso:editar-clubes');
        Route::delete('/{id}', [ClubController::class, 'destroy'])->middleware('permiso:eliminar-clubes');
    });

    Route::prefix('categorias')->group(function () {
        Route::get('/select', [CategoriaController::class, 'index']);
        Route::get('/', [CategoriaController::class, 'index'])->middleware('permiso:ver-categorias');
        Route::post('/', [CategoriaController::class, 'store'])->middleware('permiso:crear-categorias');
        Route::get('/{id}', [CategoriaController::class, 'show'])->middleware('permiso:editar-categorias');
        Route::put('/{id}', [CategoriaController::class, 'update'])->middleware('permiso:editar-categorias');
        Route::delete('/{id}', [CategoriaController::class, 'destroy'])->middleware('permiso:eliminar-categorias');
    });

    Route::prefix('tipo-accion')->group(function () {
        Route::get('/select', [TipoAccionController::class, 'index']);
        Route::get('/', [TipoAccionController::class, 'index'])->middleware('permiso:ver-tipo-accion');
        Route::post('/', [TipoAccionController::class, 'store'])->middleware('permiso:crear-tipo-accion');
        Route::get('/{id}', [TipoAccionController::class, 'show'])->middleware('permiso:editar-tipo-accion');
        Route::put('/{id}', [TipoAccionController::class, 'update'])->middleware('permiso:editar-tipo-accion');
        Route::delete('/{id}', [TipoAccionController::class, 'destroy'])->middleware('permiso:eliminar-tipo-accion');
    });

    Route::prefix('modulos')->group(function () {
        Route::get('/select', [ModuloController::class, 'index']);
        Route::get('/', [ModuloController::class, 'index'])->middleware('permiso:ver-modulos');
        Route::post('/', [ModuloController::class, 'store'])->middleware('permiso:crear-modulos');
        Route::get('/{id}', [ModuloController::class, 'show'])->middleware('permiso:editar-modulos');
        Route::put('/{id}', [ModuloController::class, 'update'])->middleware('permiso:editar-modulos');
        Route::delete('/{id}', [ModuloController::class, 'destroy'])->middleware('permiso:eliminar-modulos');
    });

    Route::prefix('deportistas')->group(function () {
        Route::get('/', [DeportistaController::class, 'index'])->middleware('permiso:ver-deportistas');
        Route::post('/', [DeportistaController::class, 'store'])->middleware('permiso:crear-deportistas');
        Route::get('/{id}', [DeportistaController::class, 'show'])->middleware('permiso:editar-deportistas');
        Route::put('/{id}', [DeportistaController::class, 'update'])->middleware('permiso:editar-deportistas');
        Route::delete('/{id}', [DeportistaController::class, 'destroy'])->middleware('permiso:eliminar-deportistas');
        Route::post('/actualizar/categoria/{id}', [DeportistaController::class, 'actualizarCategoria'])->middleware('permiso:editar-deportistas');
        Route::get('/sincronizar-fide/{fideId}', [DeportistaController::class, 'sincronizarFide']);
    });

    Route::prefix('entrenadores')->group(function () {
        Route::get('/', [EntrenadorController::class, 'index'])->middleware('permiso:ver-entrenadores');
        Route::post('/', [EntrenadorController::class, 'store'])->middleware('permiso:crear-entrenadores');
        Route::get('/{id}', [EntrenadorController::class, 'show'])->middleware('permiso:editar-entrenadores');
        Route::put('/{id}', [EntrenadorController::class, 'update'])->middleware('permiso:editar-entrenadores');
        Route::delete('/{id}', [EntrenadorController::class, 'destroy'])->middleware('permiso:eliminar-entrenadores');
    });

    Route::prefix('entrenamientos')->group(function () {
        Route::get('/', [EntrenamientoController::class, 'index']);
        Route::get('/mis-entrenamientos', [EntrenamientoController::class, 'misEntrenamientos']);

        Route::post('/{id}/google', [EntrenamientoController::class, 'syncToGoogle']);
        Route::get('/google/authorize', [GoogleCalendarController::class, 'redirectToGoogle']);
    });

    Route::prefix('/eventos')->group(function () {
        Route::get('/', [EventoController::class, 'index'])->middleware('permiso:ver-eventos');
        Route::post('/', [EventoController::class, 'store'])->middleware('permiso:crear-eventos');
        Route::get('/{id}', [EventoController::class, 'show'])->middleware('permiso:editar-eventos');
        Route::put('/{id}', [EventoController::class, 'update'])->middleware('permiso:editar-eventos');
        Route::delete('/{id}', [EventoController::class, 'destroy'])->middleware('permiso:eliminar-eventos');
    });

    Route::prefix('/torneos')->group(function () {
        Route::get('/', [TorneoController::class, 'index'])->middleware('permiso:ver-eventos');
        Route::post('/', [TorneoController::class, 'store'])->middleware('permiso:crear-eventos');
        Route::get('/{id}', [TorneoController::class, 'show'])->middleware('permiso:editar-eventos');
        Route::put('/{id}', [TorneoController::class, 'update'])->middleware('permiso:editar-eventos');
        Route::delete('/{id}', [TorneoController::class, 'destroy'])->middleware('permiso:eliminar-eventos');
    });


