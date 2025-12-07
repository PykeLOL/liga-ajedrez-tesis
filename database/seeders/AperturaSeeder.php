<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AperturaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $aperturas = [
            ['eco' => 'A00', 'nombre' => 'Zukertort Opening', 'created_at' => now(), 'updated_at' => now()],
            ['eco' => 'A20', 'nombre' => 'Apertura Inglesa', 'created_at' => now(), 'updated_at' => now()],
            ['eco' => 'A40', 'nombre' => 'Apertura Irregular', 'created_at' => now(), 'updated_at' => now()],
            ['eco' => 'B00', 'nombre' => 'Defensa Moderna', 'created_at' => now(), 'updated_at' => now()],
            ['eco' => 'B20', 'nombre' => 'Defensa Siciliana', 'created_at' => now(), 'updated_at' => now()],
            ['eco' => 'B30', 'nombre' => 'Siciliana Alapin', 'created_at' => now(), 'updated_at' => now()],
            ['eco' => 'B40', 'nombre' => 'Siciliana Paulsen', 'created_at' => now(), 'updated_at' => now()],
            ['eco' => 'C00', 'nombre' => 'Defensa Francesa', 'created_at' => now(), 'updated_at' => now()],
            ['eco' => 'C20', 'nombre' => 'Apertura del Rey (e4)', 'created_at' => now(), 'updated_at' => now()],
            ['eco' => 'C30', 'nombre' => 'Apertura Vienesa', 'created_at' => now(), 'updated_at' => now()],
            ['eco' => 'C40', 'nombre' => 'Defensa de los Dos Caballos', 'created_at' => now(), 'updated_at' => now()],
            ['eco' => 'C50', 'nombre' => 'Giuoco Piano', 'created_at' => now(), 'updated_at' => now()],
            ['eco' => 'D00', 'nombre' => 'Apertura Peón Dama', 'created_at' => now(), 'updated_at' => now()],
            ['eco' => 'D20', 'nombre' => 'Gambito de Dama Aceptado', 'created_at' => now(), 'updated_at' => now()],
            ['eco' => 'D30', 'nombre' => 'Gambito de Dama Rehusado', 'created_at' => now(), 'updated_at' => now()],
            ['eco' => 'D40', 'nombre' => 'Gambito de Dama Semieslavo', 'created_at' => now(), 'updated_at' => now()],
            ['eco' => 'E00', 'nombre' => 'Apertura Catalana', 'created_at' => now(), 'updated_at' => now()],
            ['eco' => 'E20', 'nombre' => 'Defensa Nimzo-India', 'created_at' => now(), 'updated_at' => now()],
            ['eco' => 'E60', 'nombre' => 'Defensa India de Rey', 'created_at' => now(), 'updated_at' => now()],
            ['eco' => 'E90', 'nombre' => 'India de Rey - Variante Clásica', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('aperturas')->insert($aperturas);
    }
}
