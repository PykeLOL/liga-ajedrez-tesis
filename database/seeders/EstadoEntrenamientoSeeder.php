<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EstadoEntrenamiento;

class EstadoEntrenamientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $estados = [
            ['id' => 1, 'nombre' => 'Programado'],
            ['id' => 2, 'nombre' => 'En Curso'],
            ['id' => 3, 'nombre' => 'Finalizado'],
            ['id' => 4, 'nombre' => 'Cancelado'],
        ];

        foreach ($estados as $estado) {
            EstadoEntrenamiento::firstOrCreate(['nombre' => $estado['nombre']], $estado);
        }
    }
}
