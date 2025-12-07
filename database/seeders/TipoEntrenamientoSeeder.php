<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoEntrenamientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tiposEntrenamiento = [
            ['nombre' => 'Táctico', 'descripcion' => 'Enfoque en táctica y estrategia'],
            ['nombre' => 'Técnico', 'descripcion' => 'Entrenamiento de fundamentos técnicos'],
            ['nombre' => 'Físico', 'descripcion' => 'Condición física y resistencia'],
            ['nombre' => 'Partidas de práctica', 'descripcion' => 'Juegos simulados'],
            ['nombre' => 'Análisis de partidas', 'descripcion' => 'Revisión y análisis de partidas anteriores'],
            ['nombre' => 'Psicológico', 'descripcion' => 'Preparación mental y concentración'],
            ['nombre' => 'Teórico', 'descripcion' => 'Estudio de aperturas y finales'],
        ];

        DB::table('tipos_entrenamiento')->insert($tiposEntrenamiento);
    }
}
