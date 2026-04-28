<?php

namespace Tests;

use App\Models\Permiso;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Helper para asignar permisos
     */
    protected function asignarPermisoARol($rol, $accion, $modulo)
    {
        // Buscar o crear tipo_accion
        $tipoAccion = DB::table('tipo_accion')
            ->where('nombre', $accion)
            ->first();

        if (!$tipoAccion) {
            $tipoAccionId = DB::table('tipo_accion')->insertGetId([
                'nombre' => $accion,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $tipoAccionId = $tipoAccion->id;
        }

        // Buscar o crear modulo
        $mod = DB::table('modulos')
            ->where('nombre', $modulo)
            ->first();

        if (!$mod) {
            $moduloId = DB::table('modulos')->insertGetId([
                'nombre' => $modulo,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $moduloId = $mod->id;
        }

        // Crear permiso (evitar duplicados)
        $permiso = Permiso::firstOrCreate([
            'nombre' => "{$accion}-{$modulo}",
        ], [
            'descripcion' => "Permiso {$accion} {$modulo}",
            'tipo_accion_id' => $tipoAccionId,
            'modulo_id' => $moduloId,
        ]);

        // Asignar al rol (evitar duplicado)
        DB::table('roles_permisos')->updateOrInsert([
            'rol_id' => $rol->id,
            'permiso_id' => $permiso->id,
        ], [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $permiso;
    }
}
