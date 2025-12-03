<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CertificacionEntrenadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $rolEntrenadorId = DB::table('roles')->where('nombre', 'Entrenador')->value('id');
        $usuarioEntrenadorId = DB::table('usuarios')->where('rol_id', $rolEntrenadorId)->value('id');
        $entrenadorId = DB::table('entrenadores')->where('usuario_id', $usuarioEntrenadorId)->value('id');
        $fideId = DB::table('entidades_certificacion')->where('nombre', 'FIDE')->value('id');
        $fecodazId = DB::table('entidades_certificacion')->where('nombre', 'FECODAZ')->value('id');
        $fedaId = DB::table('entidades_certificacion')->where('nombre', 'FEDA')->value('id');

        $certificaciones = [
            [
                'nombre' => 'FIDE Trainer',
                'entidad_id' => $fideId,
                'descripcion' => 'Certificación oficial de entrenador avalada por la FIDE.',
                'entrenador_id' => $entrenadorId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Entrenador Nacional Nivel 1',
                'entidad_id' => $fecodazId,
                'descripcion' => 'Certificación nacional para formación de deportistas sub-20.',
                'entrenador_id' => $entrenadorId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Curso de Preparación para Torneos Internacionales',
                'entidad_id' => $fedaId,
                'descripcion' => 'Entrenamiento profesional para competencias internacionales.',
                'entrenador_id' => $entrenadorId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('certificaciones_entrenador')->insert($certificaciones);
    }
}
