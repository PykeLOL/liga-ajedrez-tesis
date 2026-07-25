<?php

namespace Database\Seeders;

use App\Models\EstadoPlan;
use Illuminate\Database\Seeder;

class EstadoPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $estados = [
            ['id' => 1, 'nombre' => 'Activo'],
            ['id' => 2, 'nombre' => 'Borrador'],
            ['id' => 3, 'nombre' => 'Finalizado'],
            ['id' => 4, 'nombre' => 'Cancelado'],
        ];

        foreach ($estados as $estado) {
            EstadoPlan::firstOrCreate(['nombre' => $estado['nombre']], $estado);
        }
    }
}
