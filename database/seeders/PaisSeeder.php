<?php

namespace Database\Seeders;

use App\Models\Pais;
use Illuminate\Database\Seeder;

class PaisSeeder extends Seeder
{
    public function run()
    {
        $paises = [
            [
                'nombre' => 'Colombia',
            ],
        ];

        foreach ($paises as $pais) {
            Pais::firstOrCreate(['nombre' => $pais['nombre']], $pais);
        }
    }
}
