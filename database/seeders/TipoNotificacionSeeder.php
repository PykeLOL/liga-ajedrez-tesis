<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoNotificacion;

class TipoNotificacionSeeder extends Seeder
{
    public function run()
    {
        $tiposNotificacion = [
            [
                'id' => 1,
                'nombre' => 'Aprobación',
                'icono' => 'badge-check',
            ],
            [
                'id' => 2,
                'nombre' => 'Rechazo',
                'icono' => 'circle-x',
            ],
            [
                'id' => 3,
                'nombre' => 'Comentario',
                'icono' => 'message-square',
            ],
            [
                'id' => 4,
                'nombre' => 'Evento',
                'icono' => 'calendar-days',
            ],
            [
                'id' => 5,
                'nombre' => 'Entrenamiento',
                'icono' => 'biceps-flexed',
            ],
            [
                'id' => 6,
                'nombre' => 'Sistema',
                'icono' => 'crown',
            ],
            [
                'id' => 7,
                'nombre' => 'Solicitud Club',
                'icono' => 'building-2',
            ],
            [
                'id' => 8,
                'nombre' => 'Solicitud Deportista',
                'icono' => 'user-plus',
            ],
        ];

        foreach ($tiposNotificacion as $tipo) {
            TipoNotificacion::firstOrCreate(['id' => $tipo['id']], $tipo);
        }
    }
}
