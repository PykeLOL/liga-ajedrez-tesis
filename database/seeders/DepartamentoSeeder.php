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

        require database_path('seeders/data/DepartamentosMunicipiosColombia.php');

        foreach ($municipios as $departamento => $listaMunicipios) {

            $nombreDepartamento = $this->nombreDepartamento($departamento);

            Departamento::firstOrCreate(
                [
                    'nombre' => $nombreDepartamento,
                ],
                [
                    'nombre' => $nombreDepartamento,
                    'pais_id' => $paisId,
                ]
            );
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
