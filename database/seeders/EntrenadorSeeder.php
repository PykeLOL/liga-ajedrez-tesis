<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntrenadorSeeder extends Seeder
{
    public function run(): void
    {
        $generoId = DB::table('generos')
            ->where('nombre', 'Masculino')
            ->value('id');

        $nacionalidadId = DB::table('nacionalidades')
            ->where('nombre', 'Colombia')
            ->value('id');

        $entrenadores = [
            [
                'email' => 'carlos.rodriguez@chess.com',
                'club' => 'Titan Chess',
                'fecha_nacimiento' => '1985-06-10',
                'experiencia_anios' => 10,
                'especialidad' => 'Entrenamiento de alto rendimiento y preparación de torneos.',
            ],
            [
                'email' => 'andres.morales@chess.com',
                'club' => 'Talentos Ajedrez',
                'fecha_nacimiento' => '1988-03-22',
                'experiencia_anios' => 8,
                'especialidad' => 'Formación de jóvenes talentos y preparación competitiva.',
            ],
            [
                'email' => 'diego.castro@chess.com',
                'club' => 'Magistral',
                'fecha_nacimiento' => '1982-11-17',
                'experiencia_anios' => 15,
                'especialidad' => 'Estrategia, análisis de partidas y preparación para competencias.',
            ],
            [
                'email' => 'felipe.vargas@chess.com',
                'club' => 'Rey Dama',
                'fecha_nacimiento' => '1990-07-05',
                'experiencia_anios' => 7,
                'especialidad' => 'Técnica de finales, táctica y preparación de jugadores.',
            ],
        ];

        foreach ($entrenadores as $data) {
            $usuarioId = DB::table('usuarios')
                ->where('email', $data['email'])
                ->value('id');

            $clubId = DB::table('clubes')
                ->where('nombre', $data['club'])
                ->value('id');

            if (!$usuarioId || !$clubId) {
                continue;
            }

            DB::table('entrenadores')->updateOrInsert(
                ['usuario_id' => $usuarioId],
                [
                    'club_id' => $clubId,
                    'fecha_nacimiento' => $data['fecha_nacimiento'],
                    'genero_id' => $generoId,
                    'nacionalidad_id' => $nacionalidadId,
                    'experiencia_anios' => $data['experiencia_anios'],
                    'especialidad' => $data['especialidad'],
                    'estado' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
