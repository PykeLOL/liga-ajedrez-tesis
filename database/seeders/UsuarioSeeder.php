<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolAdminId = DB::table('roles')->where('nombre', 'Admin')->value('id');
        $rolPresidenteLigaId = DB::table('roles')->where('nombre', 'Presidente Liga')->value('id');
        $rolDeportistaId = DB::table('roles')->where('nombre', 'Deportista')->value('id');
        $rolEntrenadorId = DB::table('roles')->where('nombre', 'Entrenador')->value('id');
        $tipoIdentificacionId = DB::table('tipos_identificacion')->where('abreviacion', 'CC')->value('id');

        $users = [
            [
                'nombre' => 'admin',
                'apellido' => 'admin',
                'email' => 'admin@admin.com',
                'tipo_identificacion_id' => $tipoIdentificacionId,
                'numero_identificacion' => '123456789',
                'contraseña' => Hash::make('admin'),
                'rol_id' => $rolAdminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Nelson',
                'apellido' => 'Arango',
                'email' => 'presidente@gmail.com',
                'tipo_identificacion_id' => $tipoIdentificacionId,
                'numero_identificacion' => '1121967543',
                'contraseña' => Hash::make('presidente'),
                'rol_id' => $rolPresidenteLigaId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Brahian',
                'apellido' => 'Pulido',
                'email' => 'deportista@gmail.com',
                'tipo_identificacion_id' => $tipoIdentificacionId,
                'numero_identificacion' => '987654321',
                'contraseña' => Hash::make('deportista'),
                'rol_id' => $rolDeportistaId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Juan',
                'apellido' => 'Lopez',
                'email' => 'entrenador@gmail.com',
                'tipo_identificacion_id' => $tipoIdentificacionId,
                'numero_identificacion' => '534634543',
                'contraseña' => Hash::make('entrenador'),
                'rol_id' => $rolEntrenadorId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('usuarios')->insert($users);
    }
}
