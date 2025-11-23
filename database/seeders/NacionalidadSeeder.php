<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NacionalidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $nacionalidades = [
            ['nombre' => 'Colombia', 'codigo' => 'COL', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ecuador', 'codigo' => 'ECU', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Perú', 'codigo' => 'PER', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Venezuela', 'codigo' => 'VEN', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Argentina', 'codigo' => 'ARG', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Brasil', 'codigo' => 'BRA', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'México', 'codigo' => 'MEX', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'España', 'codigo' => 'ESP', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Estados Unidos', 'codigo' => 'USA', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('nacionalidades')->insert($nacionalidades);
    }
}
