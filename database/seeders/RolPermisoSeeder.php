<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolPermisoSeeder extends Seeder
{
    private const PERMISOS = [

        'Admin' => [
            '*' => ['*'],
        ],

        'Presidente Liga' => [
            'clubes' => ['ver', 'crear', 'editar', 'eliminar'],
            'eventos' => ['ver', 'crear', 'editar', 'eliminar'],
            'categorias' => ['ver', 'crear', 'editar', 'eliminar'],
            'generos' => ['ver', 'crear', 'editar', 'eliminar'],
            'deportistas' => ['ver', 'crear', 'editar', 'eliminar'],
            'entrenadores' => ['ver', 'crear', 'editar', 'eliminar'],
            'titulos' => ['ver', 'crear', 'editar', 'eliminar'],
            'planes-entrenamiento' => ['ver', 'crear', 'editar', 'eliminar'],
            'entrenamientos' => ['ver', 'crear', 'editar', 'eliminar'],
            'solicitudes' => ['ver', 'editar', 'autorizar'],
            'ligas' => ['ver', 'editar'],
            'usuarios' => ['ver'],
            'roles' => ['ver'],
        ],

        'Presidente Club' => [
            'clubes' => ['ver', 'editar'],
            'categorias' => ['ver'],
            'generos' => ['ver'],
            'titulos' => ['ver'],
            'usuarios' => ['ver'],
            'deportistas' => ['ver', 'crear', 'editar'],
            'entrenadores' => ['ver', 'crear', 'editar'],
            'eventos' => ['ver', 'crear', 'editar'],
            'planes-entrenamiento' => ['ver', 'crear', 'editar'],
            'entrenamientos' => ['ver', 'crear', 'editar'],
            'solicitudes' => ['ver', 'editar', 'autorizar'],
        ],

        'Director' => [
            'clubes' => ['ver'],
            'categorias' => ['ver'],
            'generos' => ['ver'],
            'titulos' => ['ver'],
            'usuarios' => ['ver'],
            'deportistas' => ['ver', 'crear', 'editar'],
            'entrenadores' => ['ver', 'crear', 'editar'],
            'eventos' => ['ver', 'crear', 'editar'],
            'planes-entrenamiento' => ['ver'],
            'entrenamientos' => ['ver'],
        ],

        'Entrenador' => [
            'clubes' => ['ver'],
            'categorias' => ['ver'],
            'generos' => ['ver'],
            'titulos' => ['ver'],
            'entrenadores' => ['ver'],
            'eventos' => ['ver'],
            'deportistas' => ['ver', 'editar'],
            'planes-entrenamiento' => ['ver'],
            'entrenamientos' => ['ver'],
        ],

        'Deportista' => [
            'eventos' => ['ver'],
            'entrenamientos' => ['ver'],
        ],
    ];

    public function run(): void
    {
        DB::table('roles_permisos')->truncate();

        $roles = DB::table('roles')->pluck('id', 'nombre');

        $insert = [];

        foreach (self::PERMISOS as $rol => $modulos) {

            if (!isset($roles[$rol])) {
                continue;
            }

            if (isset($modulos['*'])) {
                $this->asignarTodos($insert, $roles[$rol]);
                continue;
            }

            foreach ($modulos as $modulo => $acciones) {
                $this->asignarPermisos($insert, $roles[$rol], $modulo, $acciones);
            }
        }

        DB::table('roles_permisos')->insert($insert);
    }

    private function asignarTodos(array &$insert, int $rolId): void
    {
        foreach (DB::table('permisos')->pluck('id') as $permisoId) {
            $insert[] = $this->row($rolId, $permisoId);
        }
    }

    private function asignarPermisos(array &$insert, int $rolId, string $modulo, array $acciones): void
    {
        $permisos = DB::table('permisos')
            ->whereIn(
                'nombre',
                array_map(fn($accion) => "{$accion}-{$modulo}", $acciones)
            )
            ->pluck('id');

        foreach ($permisos as $permisoId) {
            $insert[] = $this->row($rolId, $permisoId);
        }
    }

    private function row(int $rolId, int $permisoId): array
    {
        return [
            'rol_id' => $rolId,
            'permiso_id' => $permisoId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
