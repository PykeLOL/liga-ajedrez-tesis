<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiaSemanaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $diasSemana = [
            ['nombre' => 'Lunes', 'numero' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Martes', 'numero' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Miércoles', 'numero' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Jueves', 'numero' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Viernes', 'numero' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Sábado', 'numero' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Domingo ', 'numero' => 7, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('dias_semana')->insert($diasSemana);
    }
}
