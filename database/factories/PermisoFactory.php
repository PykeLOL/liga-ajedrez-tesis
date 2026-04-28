<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class PermisoFactory extends Factory
{
    public function definition()
    {
        // Crear tipo_accion simple si no existe
        $tipoAccionId = DB::table('tipo_accion')->insertGetId([
            'nombre' => 'crear',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear modulo simple si no existe
        $moduloId = DB::table('modulos')->insertGetId([
            'nombre' => 'usuarios',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'nombre' => 'crear-usuarios',
            'descripcion' => 'Permiso test',
            'tipo_accion_id' => $tipoAccionId,
            'modulo_id' => $moduloId,
        ];
    }

    /**
     * State dinámico para cualquier permiso
     */
    public function permiso($accion, $modulo)
    {
        return $this->state(function () use ($accion, $modulo) {

            $tipoAccionId = DB::table('tipo_accion')->insertGetId([
                'nombre' => $accion,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $moduloId = DB::table('modulos')->insertGetId([
                'nombre' => $modulo,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return [
                'nombre' => "{$accion}-{$modulo}",
                'descripcion' => "Permiso {$accion} {$modulo}",
                'tipo_accion_id' => $tipoAccionId,
                'modulo_id' => $moduloId,
            ];
        });
    }
}
