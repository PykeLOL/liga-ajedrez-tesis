<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntidadCertificacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entidades = [
            [
                'nombre' => 'FIDE',
                'descripcion' => 'Federación Internacional de Ajedrez',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'FEDA',
                'descripcion' => 'Federación Española de Ajedrez',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'FECODAZ',
                'descripcion' => 'Federación Colombiana de Ajedrez',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'CAPAC',
                'descripcion' => 'Confederación de Ajedrez para América',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'IDT',
                'descripcion' => 'Instituto de Recreación y Deporte',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Liga Departamental de Ajedrez',
                'descripcion' => 'Ligas de diferentes departamentos o regiones',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        DB::table('entidades_certificacion')->insert($entidades);
    }
}
