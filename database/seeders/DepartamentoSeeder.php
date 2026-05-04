<?php

namespace Database\Seeders;

use App\Models\Pais;
use App\Models\Departamento;
use Illuminate\Database\Seeder;

class DepartamentoSeeder extends Seeder
{
    public function run()
    {
        $paisId = Pais::where('nombre', 'Colombia')->value('id');

        $departamentos = [
            [
                'nombre' => 'Meta',
                'pais_id' => $paisId,
            ],
        ];

        foreach ($departamentos as $departamento) {
            Departamento::firstOrCreate(['nombre' => $departamento['nombre']], $departamento);
        }
    }
}
