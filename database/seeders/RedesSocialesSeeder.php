<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RedesSocialesSeeder extends Seeder
{
    public function run()
    {
        $redes = [
            [
                'nombre' => 'Facebook',
                'icono'  => 'fa-brands fa-facebook',
            ],
            [
                'nombre' => 'Instagram',
                'icono'  => 'fa-brands fa-instagram',
            ],
            [
                'nombre' => 'X',
                'icono'  => 'fa-brands fa-x-twitter',
            ],
            [
                'nombre' => 'YouTube',
                'icono'  => 'fa-brands fa-youtube',
            ],
        ];

        foreach ($redes as $red) {
            DB::table('redes_sociales')->updateOrInsert(
                ['nombre' => $red['nombre']],
                [
                    'icono' => $red['icono'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
