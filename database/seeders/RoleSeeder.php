<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'Admin',
                'descripcion' => 'Control total sobre el sistema y la administración de la plataforma.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Presidente Liga',
                'descripcion' => 'Administración y gestión de su liga deportiva asociada.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Presidente Club',
                'descripcion' => 'Administración y gestión de su club deportivo asociado.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Director',
                'descripcion' => 'Coordinación de actividades y personal dentro de una estructura específica (ej. Director Deportivo).',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Entrenador',
                'descripcion' => 'Gestión de equipos, planificación de entrenamientos y desarrollo de deportistas.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Deportista',
                'descripcion' => 'Acceso limitado para ver su perfil, horarios y resultados.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('roles')->insert($roles);
    }
}