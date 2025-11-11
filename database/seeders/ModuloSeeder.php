<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modulos = [
            [
                'nombre' => 'usuarios',
                'descripcion' => 'Gestión de usuarios del sistema (creación, edición y eliminación).',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'roles',
                'descripcion' => 'Administración de roles y sus permisos asociados.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'permisos',
                'descripcion' => 'Configuración de permisos asignados a cada rol o usuario.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'tipo-accion',
                'descripcion' => 'Definición de los tipos de acciones disponibles en el sistema (ver, crear, editar, eliminar).',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'modulos',
                'descripcion' => 'Gestión de los módulos principales del sistema.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'entrenadores',
                'descripcion' => 'Registro y administración de los entrenadores deportivos.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'ligas',
                'descripcion' => 'Gestión de ligas deportivas y sus asociaciones con clubes.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'clubes',
                'descripcion' => 'Administración de los clubes deportivos registrados en el sistema.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'categorias',
                'descripcion' => 'Gestión de las categorías por edad o nivel deportivo.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'generos',
                'descripcion' => 'Listado y administración de géneros deportivos.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'nacionalidades',
                'descripcion' => 'Catálogo de nacionalidades de los deportistas y entrenadores.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'tipos-identificacion',
                'descripcion' => 'Tipos de documentos de identificación (CC, CE, Pasaporte, etc.).',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'titulos',
                'descripcion' => 'Registro de títulos y reconocimientos otorgados a deportistas.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'deportistas',
                'descripcion' => 'Administración de la información de los deportistas registrados.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'perfil',
                'descripcion' => 'Administración la informacion de mi Perfil.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('modulos')->insert($modulos);
    }
}
