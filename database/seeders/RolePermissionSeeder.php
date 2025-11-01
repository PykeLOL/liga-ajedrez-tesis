<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRolId = DB::table('roles')->where('nombre', 'Admin')->value('id');

        if (!$adminRolId) {
            return;
        }

        $verPermisosIds = DB::table('permisos')
                            ->where('nombre', 'LIKE', 'ver-%')
                            ->pluck('id');

        $permisosUsuariosId = DB::table('permisos')
                            ->where('nombre', 'permisos-usuarios')
                            ->pluck('id');
        
        $permisosRolesId = DB::table('permisos')
                            ->where('nombre', 'permisos-roles')
                            ->pluck('id');

        $permisosParaAdmin = $verPermisosIds->merge($permisosUsuariosId)->unique();
        $permisosParaAdmin = $permisosParaAdmin->merge($permisosRolesId)->unique();
        $rolePermissions = [];
        
        foreach ($permisosParaAdmin as $permisoId) {
            $rolePermissions[] = [
                'rol_id' => $adminRolId,
                'permiso_id' => $permisoId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('roles_permisos')->insert($rolePermissions);
    }
}