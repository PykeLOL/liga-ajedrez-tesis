<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoEventoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipos = [
            [
                'nombre' => 'Torneo',
                'abreviacion' => 'TOR',
                'descripcion' => 'Competencias formales de ajedrez.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Festival',
                'abreviacion' => 'FES',
                'descripcion' => 'Eventos recreativos y competitivos mixtos.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Simultánea',
                'abreviacion' => 'SIM',
                'descripcion' => 'Exhibiciones simultáneas de ajedrez.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Capacitación',
                'abreviacion' => 'CAP',
                'descripcion' => 'Cursos, talleres o charlas de formación.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Otro',
                'abreviacion' => 'OTR',
                'descripcion' => 'Eventos no clasificados.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tipos_evento')->insert($tipos);
    }
}
