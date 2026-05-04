<?php

namespace Database\Seeders;

use App\Models\Reaccion;
use Illuminate\Database\Seeder;

class ReaccionSeeder extends Seeder
{
    public function run()
    {
        $reacciones = [
            [
                'nombre' => 'Me gusta',
                'icono' => 'hand-thumbs-up',
            ],
            [
                'nombre' => 'No me gusta',
                'icono' => 'hand-thumbs-down',
            ],
            [
                'nombre' => 'Me encanta',
                'icono' => 'heart',
            ],
            [
                'nombre' => 'Me divierte',
                'icono' => 'emoji-laughing',
            ],
            [
                'nombre' => 'Me sorprende',
                'icono' => 'emoji-surprise',
            ],
            [
                'nombre' => 'Me entristece',
                'icono' => 'emoji-frown',
            ],
        ];

        foreach ($reacciones as $reaccion) {
            Reaccion::firstOrCreate(['nombre' => $reaccion['nombre']], $reaccion);
        }
    }
}
