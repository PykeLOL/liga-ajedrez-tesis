<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntrenadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rolEntrenadorId = DB::table('roles')->where('nombre', 'Entrenador')->value('id');
        $usuarioEntrenadorId = DB::table('usuarios')->where('rol_id', $rolEntrenadorId)->value('id');
        $generoId = DB::table('generos')->where('nombre', 'Masculino')->value('id');
        $nacionalidadId = DB::table('nacionalidades')->where('nombre', 'Colombia')->value('id');
        $clubId = DB::table('clubes')->where('nombre', 'Club Titan Chess')->value('id');

        $entrenadores = [
            [
                'usuario_id' => $usuarioEntrenadorId,
                'club_id' => $clubId,
                'fecha_nacimiento' => '1985-06-10',
                'genero_id' => $generoId,
                'nacionalidad_id' => $nacionalidadId,
                'experiencia_anios' => 10,
                'especialidad' => 'Entrenamiento de alto rendimiento y preparación de torneos.',
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('entrenadores')->insert($entrenadores);
    }
}
