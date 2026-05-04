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
                // 'id' => 1,
                'nombre' => 'admin',
                'apellido' => 'admin',
                'email' => 'admin@admin.com',
                'tipo_identificacion_id' => $tipoIdentificacionId,
                'numero_identificacion' => '123456789',
                'contraseña' => Hash::make('admin'),
                'rol_id' => $rolAdminId,
                'imagen_path' => 'usuarios/admin.png',
                'telefono' => '3178711309',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 'id' => 2,
                'nombre' => 'Nelson',
                'apellido' => 'Arango',
                'email' => 'presidente@gmail.com',
                'tipo_identificacion_id' => $tipoIdentificacionId,
                'numero_identificacion' => '1121967543',
                'contraseña' => Hash::make('presidente'),
                'rol_id' => $rolPresidenteLigaId,
                'imagen_path' => 'usuarios/nelson.png',
                'telefono' => '3178711309',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 'id' => 3,
                'nombre' => 'Brahian',
                'apellido' => 'Pulido',
                'email' => 'deportista@gmail.com',
                'tipo_identificacion_id' => $tipoIdentificacionId,
                'numero_identificacion' => '987654321',
                'contraseña' => Hash::make('deportista'),
                'rol_id' => $rolDeportistaId,
                'imagen_path' => 'usuarios/brahian.png',
                'telefono' => '3001234567',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 'id' => 4,
                'nombre' => 'Juan',
                'apellido' => 'Lopez',
                'email' => 'entrenador@gmail.com',
                'tipo_identificacion_id' => $tipoIdentificacionId,
                'numero_identificacion' => '534634543',
                'contraseña' => Hash::make('entrenador'),
                'rol_id' => $rolEntrenadorId,
                'imagen_path' => 'usuarios/juan.png',
                'telefono' => '3154327676',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 'id' => 5,
                'nombre' => 'Carlsen',
                'apellido' => 'Magnus',
                'email' => 'carlsen.magnus@gmail.com',
                'tipo_identificacion_id' => $tipoIdentificacionId,
                'numero_identificacion' => '123232312',
                'contraseña' => Hash::make('deportista'),
                'rol_id' => $rolDeportistaId,
                'imagen_path' => 'usuarios/carlsen.png',
                'telefono' => '3211232323',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 'id' => 6,
                'nombre' => 'Jose Gabriel',
                'apellido' => 'Cardoso Cardoso',
                'email' => 'jose.cardoso@gmail.com',
                'tipo_identificacion_id' => $tipoIdentificacionId,
                'numero_identificacion' => '3211232323',
                'contraseña' => Hash::make('deportista'),
                'rol_id' => $rolDeportistaId,
                'imagen_path' => 'usuarios/jose-cardoso.png',
                'telefono' => '3211226666',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('usuarios')->insert($users);
        DB::statement("
            SELECT setval(
                pg_get_serial_sequence('usuarios', 'id'),
                (SELECT MAX(id) FROM usuarios)
            )
        ");
    }
}
