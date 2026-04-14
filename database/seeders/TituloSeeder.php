<?php

namespace Database\Seeders;

use App\Models\Titulo;
use Illuminate\Database\Seeder;

class TituloSeeder extends Seeder
{
    public function run()
    {
        $titulos = [
            // No FIDE
            [
                'nombre' => 'Sin título',
                'nombre_fide' => null,
                'abreviacion' => 'ST',
                'es_fide' => false
            ],
            [
                'nombre' => 'Principiante',
                'nombre_fide' => null,
                'abreviacion' => 'PR',
                'es_fide' => false
            ],
            [
                'nombre' => 'Intermedio',
                'nombre_fide' => null,
                'abreviacion' => 'IN',
                'es_fide' => false
            ],
            [
                'nombre' => 'Avanzado',
                'nombre_fide' => null,
                'abreviacion' => 'AV',
                'es_fide' => false
            ],

            // FIDE
            [
                'nombre' => 'Gran Maestro',
                'nombre_fide' => 'Grandmaster',
                'abreviacion' => 'GM',
                'es_fide' => true
            ],
            [
                'nombre' => 'Maestro Internacional',
                'nombre_fide' => 'International Master',
                'abreviacion' => 'IM',
                'es_fide' => true
            ],
            [
                'nombre' => 'Maestro FIDE',
                'nombre_fide' => 'FIDE Master',
                'abreviacion' => 'FM',
                'es_fide' => true
            ],
            [
                'nombre' => 'Candidato a Maestro',
                'nombre_fide' => 'Candidate Master',
                'abreviacion' => 'CM',
                'es_fide' => true
            ],
            [
                'nombre' => 'Gran Maestra',
                'nombre_fide' => 'Woman Grandmaster',
                'abreviacion' => 'WGM',
                'es_fide' => true
            ],
            [
                'nombre' => 'Maestra Internacional',
                'nombre_fide' => 'Woman International Master',
                'abreviacion' => 'WIM',
                'es_fide' => true
            ],
            [
                'nombre' => 'Maestra FIDE',
                'nombre_fide' => 'Woman FIDE Master',
                'abreviacion' => 'WFM',
                'es_fide' => true
            ],
            [
                'nombre' => 'Candidata a Maestra',
                'nombre_fide' => 'Woman Candidate Master',
                'abreviacion' => 'WCM',
                'es_fide' => true
            ],
        ];

        foreach ($titulos as $titulo) {
            Titulo::updateOrCreate(
                ['nombre' => $titulo['nombre']],
                array_merge($titulo, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }
    }
}
