<?php

namespace Database\Seeders;

use App\Models\Municipio;
use App\Models\Departamento;
use Illuminate\Database\Seeder;

class MunicipioSeeder extends Seeder
{
    public function run()
    {
        require database_path('seeders/data/DepartamentosMunicipiosColombia.php');

        $departamentos = Departamento::pluck('id', 'nombre')->toArray();

        foreach ($municipios as $departamento => $listaMunicipios) {

            $nombreDepartamento = $this->nombreDepartamento($departamento);

            if (!isset($departamentos[$nombreDepartamento])) {
                continue;
            }

            $departamentoId = $departamentos[$nombreDepartamento];

            foreach ($listaMunicipios as $codigo => $municipio) {

                $nombreMunicipio = trim(explode(' - ', $municipio)[0]);

                Municipio::firstOrCreate(
                    [
                        'nombre' => $nombreMunicipio,
                        'departamento_id' => $departamentoId,
                    ],
                    [
                        'nombre' => $nombreMunicipio,
                        'departamento_id' => $departamentoId,
                    ]
                );
            }
        }
    }

    private function nombreDepartamento(string $key): string
    {
        return [
            'amazonas' => 'Amazonas',
            'antioquia' => 'Antioquia',
            'arauca' => 'Arauca',
            'atlantico' => 'Atlántico',
            'bogota' => 'Bogotá D.C.',
            'bolivar' => 'Bolívar',
            'boyaca' => 'Boyacá',
            'caldas' => 'Caldas',
            'caqueta' => 'Caquetá',
            'casanare' => 'Casanare',
            'cauca' => 'Cauca',
            'cesar' => 'Cesar',
            'choco' => 'Chocó',
            'cordoba' => 'Córdoba',
            'cundinamarca' => 'Cundinamarca',
            'guainia' => 'Guainía',
            'guajira' => 'La Guajira',
            'guaviare' => 'Guaviare',
            'huila' => 'Huila',
            'magdalena' => 'Magdalena',
            'meta' => 'Meta',
            'narino' => 'Nariño',
            'norteSantander' => 'Norte de Santander',
            'putumayo' => 'Putumayo',
            'quindio' => 'Quindío',
            'risaralda' => 'Risaralda',
            'sanAndres' => 'San Andrés y Providencia',
            'santander' => 'Santander',
            'sucre' => 'Sucre',
            'tolima' => 'Tolima',
            'valle' => 'Valle del Cauca',
            'vaupes' => 'Vaupés',
            'vichada' => 'Vichada',
        ][$key];
    }
}
