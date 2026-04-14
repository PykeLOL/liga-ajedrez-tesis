<?php

namespace Database\Seeders;

use App\Models\Ritmo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RitmoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ritmos = [
            [
                'nombre' => 'Clásico',
                'descripcion' => 'Partidas de ritmo largo, normalmente superiores a 60 minutos por jugador.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Rápido',
                'descripcion' => 'Partidas con un tiempo entre 10 y 60 minutos por jugador.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Blitz',
                'descripcion' => 'Partidas rápidas con menos de 10 minutos por jugador.',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        foreach ($ritmos as $ritmo) {
            Ritmo::firstOrCreate(['nombre' => $ritmo['nombre']], $ritmo);
        }
    }
}
