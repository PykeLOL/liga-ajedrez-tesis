<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acciones = DB::table('tipo_accion')->get();
        $modulos = DB::table('modulos')
            ->where('nombre', '!=', 'perfil')
            ->get();

        $permisos = [];

        foreach ($modulos as $modulo) {
            foreach ($acciones as $accion) {
                if (in_array($accion->nombre, ['permisos', 'descargar', 'aprobar', 'cargar'])) {
                    continue;
                }

                $permisos[] = [
                    'nombre' => "{$accion->nombre}-{$modulo->nombre}",
                    'descripcion' => "Permite la acción de {$accion->nombre} en el módulo de {$modulo->nombre}.",
                    'tipo_accion_id' => $accion->id,
                    'modulo_id' => $modulo->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        $especiales = [
            [
                'nombre' => "permisos-usuarios",
                'descripcion' => "Permite gestionar los permisos asignados a cada usuario.",
                'tipo_accion_id' => $acciones->firstWhere('nombre', 'permisos')->id ?? null,
                'modulo_id' => $modulos->firstWhere('nombre', 'usuarios')->id ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => "permisos-roles",
                'descripcion' => "Permite gestionar los permisos asignados a cada rol.",
                'tipo_accion_id' => $acciones->firstWhere('nombre', 'permisos')->id ?? null,
                'modulo_id' => $modulos->firstWhere('nombre', 'roles')->id ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // [
            //     'nombre' => "editar-perfil",
            //     'descripcion' => "Permite al usuario editar su información personal en su perfil.",
            //     'tipo_accion_id' => $acciones->firstWhere('nombre', 'editar')->id ?? null,
            //     'modulo_id' => $modulos->firstWhere('nombre', 'usuarios')->id ?? null,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
        ];

        $permisos = array_merge($permisos, $especiales);

        DB::table('permisos')->insert($permisos);
    }
}
