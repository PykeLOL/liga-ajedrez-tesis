<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CategoriaEntrenadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rolEntrenadorId = DB::table('roles')->where('nombre', 'Entrenador')->value('id');
        $usuarioEntrenadorId = DB::table('usuarios')->where('rol_id', $rolEntrenadorId)->value('id');
        $entrenadorId = DB::table('entrenadores')->where('usuario_id', $usuarioEntrenadorId)->value('id');
        $categoriasIds = DB::table('categorias')->pluck('id')->toArray();

        $categoriaEntrenadorData = [];
        foreach ($categoriasIds as $categoriaId) {
            $categoriaEntrenadorData[] = [
                'categoria_id' => $categoriaId,
                'entrenador_id' => $entrenadorId,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('categorias_entrenador')->insert($categoriaEntrenadorData);
    }
}
