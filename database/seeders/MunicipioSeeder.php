<?php

namespace Database\Seeders;

use App\Models\Municipio;
use App\Models\Departamento;
use Illuminate\Database\Seeder;

class MunicipioSeeder extends Seeder
{
    public function run()
    {
        $departamentoId = Departamento::where('nombre', 'Meta')->value('id');
        $municipios = [
            ['nombre' => 'Villavicencio'],
            ['nombre' => 'Acacías'],
            ['nombre' => 'Barranca de Upía'],
            ['nombre' => 'Cabuyaro'],
            ['nombre' => 'Castilla La Nueva'],
            ['nombre' => 'Cubarral'],
            ['nombre' => 'Cumaral'],
            ['nombre' => 'El Calvario'],
            ['nombre' => 'El Castillo'],
            ['nombre' => 'El Dorado'],
            ['nombre' => 'Fuente de Oro'],
            ['nombre' => 'Granada'],
            ['nombre' => 'Guamal'],
            ['nombre' => 'La Macarena'],
            ['nombre' => 'La Uribe'],
            ['nombre' => 'Lejanías'],
            ['nombre' => 'Mapiripán'],
            ['nombre' => 'Mesetas'],
            ['nombre' => 'Puerto Concordia'],
            ['nombre' => 'Puerto Gaitán'],
            ['nombre' => 'Puerto Lleras'],
            ['nombre' => 'Puerto López'],
            ['nombre' => 'Puerto Rico'],
            ['nombre' => 'Restrepo'],
            ['nombre' => 'San Carlos de Guaroa'],
            ['nombre' => 'San Juan de Arama'],
            ['nombre' => 'San Juanito'],
            ['nombre' => 'San Martín'],
            ['nombre' => 'Vista Hermosa'],
        ];

        foreach ($municipios as $municipio) {

            Municipio::firstOrCreate(
                [
                    'nombre' => $municipio['nombre'],
                    'departamento_id' => $departamentoId,
                ],
                [
                    'nombre' => $municipio['nombre'],
                    'departamento_id' => $departamentoId,
                ]
            );
        }
    }
}
