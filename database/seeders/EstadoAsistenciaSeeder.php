<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EstadoAsistencia;

class EstadoAsistenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $estados = [
            ['id' => 1, 'nombre' => 'Pendiente'],
            ['id' => 2, 'nombre' => 'Asistió'],
            ['id' => 3, 'nombre' => 'No Asistió'],
            ['id' => 4, 'nombre' => 'Excusado'],
        ];

        foreach ($estados as $estado) {
            EstadoAsistencia::firstOrCreate(['nombre' => $estado['nombre']], $estado);
        }
    }
}
