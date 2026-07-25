<?php

namespace Database\Seeders;

use App\Models\EstadoEvento;
use Illuminate\Database\Seeder;

class EstadoEventoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $estados = [
            ['id' => 1, 'nombre' => 'Borrador'],
            ['id' => 2, 'nombre' => 'Publicado'],
            ['id' => 3, 'nombre' => 'En Curso'],
            ['id' => 4, 'nombre' => 'Finalizado'],
            ['id' => 5, 'nombre' => 'Cancelado'],
        ];

        foreach ($estados as $estado) {
            EstadoEvento::firstOrCreate(['nombre' => $estado['nombre']], $estado);
        }
    }
}
