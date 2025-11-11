<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoAccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $acciones = [
            [
                'nombre' => 'ver',
                'descripcion' => 'Acceso al Modulo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'crear',
                'descripcion' => 'Crear un nuevo registro.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'editar',
                'descripcion' => 'Editar un registro existente.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'eliminar',
                'descripcion' => 'Eliminar un registro existente.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'permisos',
                'descripcion' => 'Administrar los permisos.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'descargar',
                'descripcion' => 'Descarga de Documentos.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'aprobar',
                'descripcion' => 'Aprobar las Solicitudes.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'cargar',
                'descripcion' => 'Carga de Documentos.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tipo_accion')->insert($acciones);
    }
}
