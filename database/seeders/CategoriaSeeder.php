<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categorias = [
            [
                'nombre' => 'Sub-8',
                'edad_minima' => 0,
                'edad_maxima' => 8,
                'descripcion' => 'Categoría infantil para jugadores menores de 8 años.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Sub-10',
                'edad_minima' => 9,
                'edad_maxima' => 10,
                'descripcion' => 'Jugadores entre 9 y 10 años.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Sub-12',
                'edad_minima' => 11,
                'edad_maxima' => 12,
                'descripcion' => 'Jugadores entre 11 y 12 años.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Sub-14',
                'edad_minima' => 13,
                'edad_maxima' => 14,
                'descripcion' => 'Jugadores entre 13 y 14 años.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Sub-16',
                'edad_minima' => 15,
                'edad_maxima' => 16,
                'descripcion' => 'Jugadores entre 15 y 16 años.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Sub-18',
                'edad_minima' => 17,
                'edad_maxima' => 18,
                'descripcion' => 'Jugadores entre 17 y 18 años.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Mayores',
                'edad_minima' => 19,
                'edad_maxima' => 99,
                'descripcion' => 'Categoría para adultos.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Sénior',
                'edad_minima' => 50,
                'edad_maxima' => 99,
                'descripcion' => 'Categoría para jugadores de 50 años o más.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Libre',
                'edad_minima' => 0,
                'edad_maxima' => 99,
                'descripcion' => 'Categoría abierta para cualquier edad.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('categorias')->insert($categorias);
    }
}
