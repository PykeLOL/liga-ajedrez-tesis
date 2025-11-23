<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TituloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $titulos = [
            ['nombre' => 'Sin título', 'abreviacion' => 'ST', 'es_fide' => false, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Principiante', 'abreviacion' => 'PR', 'es_fide' => false, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Intermedio', 'abreviacion' => 'IN', 'es_fide' => false, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Avanzado', 'abreviacion' => 'AV', 'es_fide' => false, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Gran Maestro', 'abreviacion' => 'GM', 'es_fide' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Maestro Internacional', 'abreviacion' => 'IM', 'es_fide' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Maestro FIDE', 'abreviacion' => 'FM', 'es_fide' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Candidato a Maestro', 'abreviacion' => 'CM', 'es_fide' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Gran Maestra', 'abreviacion' => 'WGM', 'es_fide' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Maestra Internacional', 'abreviacion' => 'WIM', 'es_fide' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Maestra FIDE', 'abreviacion' => 'WFM', 'es_fide' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Candidata a Maestra', 'abreviacion' => 'WCM', 'es_fide' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('titulos')->insert($titulos);
    }
}
