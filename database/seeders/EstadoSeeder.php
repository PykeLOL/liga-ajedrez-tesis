<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $estados = [
            ['id' => 1, 'descripcion' => 'Activo'],
            ['id' => 2, 'descripcion' => 'Inactivo'],
            ['id' => 3, 'descripcion' => 'Pendiente'],
            ['id' => 4, 'descripcion' => 'Rechazado'],
        ];

        DB::table('estados')->insert($estados);
    }
}
