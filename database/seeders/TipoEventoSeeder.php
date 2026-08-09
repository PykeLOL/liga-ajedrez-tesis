<?php

namespace Database\Seeders;

use App\Models\TipoEvento;
use Illuminate\Database\Seeder;

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
                'icono' => 'bi-trophy-fill',
                'descripcion' => 'Competencias formales de ajedrez.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Noticia',
                'abreviacion' => 'NOT',
                'icono' => 'bi-newspaper',
                'descripcion' => 'Noticias de la Liga.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Reunión',
                'abreviacion' => 'REU',
                'icono' => 'bi-people-fill',
                'descripcion' => 'Reuniones de socios o miembros de la liga.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Convocatoria',
                'abreviacion' => 'CVC',
                'icono' => 'bi-megaphone-fill',
                'descripcion' => 'Convocatorias para eventos o competencias.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Festival',
                'abreviacion' => 'FES',
                'icono' => 'bi-stars',
                'descripcion' => 'Eventos recreativos y competitivos mixtos.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Simultánea',
                'abreviacion' => 'SIM',
                'icono' => 'bi-person-video3',
                'descripcion' => 'Exhibiciones simultáneas de ajedrez.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Capacitación',
                'abreviacion' => 'CAP',
                'icono' => 'bi-mortarboard-fill',
                'descripcion' => 'Cursos, talleres o charlas de formación.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Otro',
                'abreviacion' => 'OTR',
                'icono' => 'bi-grid-3x3-gap-fill',
                'descripcion' => 'Eventos no clasificados.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoEvento::updateOrCreate(
                ['nombre' => $tipo['nombre']],
                $tipo
            );
        }
    }
}
