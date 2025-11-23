<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $generos = [
            ['nombre' => 'Masculino', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Femenino', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Otro', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('generos')->insert($generos);
    }
}
