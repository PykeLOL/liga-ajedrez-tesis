<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = ['usuarios', 'roles', 'permisos', 'ligas', 'clubes'];
        $actions = ['ver', 'crear', 'editar', 'eliminar'];
        $permissions = [];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $permissions[] = [
                    'nombre' => "{$action}-{$module}",
                    'descripcion' => "Permite la acción de {$action} en el módulo de {$module}.",
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        $permissions[] = [
                    'nombre' => "permisos-usuarios",
                    'descripcion' => "Permite la acción de Editar los permisos del usuario en el módulo de usuarios.",
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

        $permissions[] = [
                    'nombre' => "permisos-roles",
                    'descripcion' => "Permite la acción de Editar los permisos del rol en el módulo de roles.",
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

        $permissions[] = [
                    'nombre' => "editar-perfil",
                    'descripcion' => "Permite la acción de Editar el perfil del usuario.",
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

        DB::table('permisos')->insert($permissions);
    }
}
