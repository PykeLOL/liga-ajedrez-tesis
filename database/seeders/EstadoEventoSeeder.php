<?php

namespace Database\Seeders;

use App\Models\EstadoEvento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
            ['nombre' => 'Borrador', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Publicado', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'En Curso', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Finalizado', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Cancelado', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($estados as $estado) {
            EstadoEvento::firstOrCreate(['nombre' => $estado['nombre']], $estado);
        }
    }
}
