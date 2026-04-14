<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolPermisoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles_permisos')->truncate();

        $roles = DB::table('roles')->pluck('id', 'nombre');

        $permisos = DB::table('permisos')->get()->groupBy(function ($permiso) {
            return $permiso->nombre;
        });

        $insert = [];

        $this->asignarTodos($insert, $roles['Admin']);

        $this->asignarPorModulo($insert, $roles['Presidente Liga'], [
            'clubes', 'eventos', 'categorias',
            'generos', 'deportistas', 'entrenadores', 'titulos'
        ], ['ver', 'crear', 'editar', 'eliminar']);

        $this->asignarPorModulo($insert, $roles['Presidente Liga'], [
            'ligas'
        ], ['ver', 'editar']);

        $this->asignarPorModulo($insert, $roles['Presidente Liga'], [
            'usuarios', 'roles'
        ], ['ver']);

        $this->asignarPorModulo($insert, $roles['Presidente Club'], [
            'deportistas', 'entrenadores', 'eventos'
        ], ['ver', 'crear', 'editar']);

        $this->asignarPorModulo($insert, $roles['Presidente Club'], [
            'clubes', 'categorias', 'generos', 'titulos', 'usuarios'
        ], ['ver']);

        $this->asignarPorModulo($insert, $roles['Director'], [
            'deportistas', 'eventos', 'entrenadores', 'eventos'
        ], ['ver', 'crear', 'editar']);

        $this->asignarPorModulo($insert, $roles['Director'], [
            'clubes', 'categorias', 'generos', 'titulos', 'usuarios'
        ], ['ver']);

        $this->asignarPorModulo($insert, $roles['Entrenador'], [
            'deportistas'
        ], ['ver', 'editar']);

        $this->asignarPorModulo($insert, $roles['Entrenador'], [
            'eventos', 'categorias', 'clubes',
            'entrenadores', 'generos', 'titulos'
        ], ['ver']);

        // $this->asignarPorModulo($insert, $roles['Deportista'], [
        //     'eventos', 'categorias', 'clubes', 'titulos'
        // ], ['ver']);

        DB::table('roles_permisos')->insert($insert);
    }

    private function asignarTodos(&$insert, $rolId)
    {
        $permisos = DB::table('permisos')->pluck('id');

        foreach ($permisos as $permisoId) {
            $insert[] = $this->row($rolId, $permisoId);
        }
    }

    private function asignarPorModulo(&$insert, $rolId, array $modulos, array $acciones)
    {
        $permisos = DB::table('permisos')
            ->where(function ($q) use ($modulos, $acciones) {
                foreach ($modulos as $modulo) {
                    foreach ($acciones as $accion) {
                        $q->orWhere('nombre', "{$accion}-{$modulo}");
                    }
                }
            })
            ->pluck('id');

        foreach ($permisos as $permisoId) {
            $insert[] = $this->row($rolId, $permisoId);
        }
    }

    private function row($rolId, $permisoId)
    {
        return [
            'rol_id' => $rolId,
            'permiso_id' => $permisoId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
