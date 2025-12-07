<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoInscripcionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $estados = [
            ['nombre' => 'Pendiente', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Pagado', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Rechazado', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Cancelado', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('estados_inscripcion')->insert($estados);
    }
}
