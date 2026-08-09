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
                'icono' => 'hand-thumbs-up-fill',
            ],
            [
                'nombre' => 'No me gusta',
                'icono' => 'hand-thumbs-down-fill',
            ],
        ];

        foreach ($reacciones as $reaccion) {
            Reaccion::firstOrCreate(['nombre' => $reaccion['nombre']], $reaccion);
        }
    }
}
