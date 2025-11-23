<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoIdentificacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tiposIdentificacion = [
            [
                'abreviacion' => 'CC',
                'nombre' => 'Cédula de Ciudadanía',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'abreviacion' => 'TI',
                'nombre' => 'Tarjeta de Identidad',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'abreviacion' => 'CE',
                'nombre' => 'Cédula de Extranjería',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'abreviacion' => 'PAS',
                'nombre' => 'Pasaporte',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'abreviacion' => 'NIT',
                'nombre' => 'Número de Identificación Tributaria',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tipos_identificacion')->insert($tiposIdentificacion);
    }
}
