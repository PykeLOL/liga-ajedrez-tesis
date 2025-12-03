<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeportistaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rolDeportistaId = DB::table('roles')->where('nombre', 'Deportista')->value('id');
        $usuarioDeportistaId = DB::table('usuarios')->where('rol_id', $rolDeportistaId)->value('id');
        $generoId = DB::table('generos')->where('nombre', 'Masculino')->value('id');
        $nacionalidadId = DB::table('nacionalidades')->where('nombre', 'Colombia')->value('id');
        $clubId = DB::table('clubes')->where('nombre', 'Club Titan Chess')->value('id');
        $fecha_nacimiento = '2001-10-15';
        $edad = Carbon::parse($fecha_nacimiento)->age;
        $categoriaId = DB::table('categorias')->where('nombre', '!=', 'Libre')
            ->where('edad_minima', '<=', $edad)
            ->where('edad_maxima', '>=', $edad)
            ->value('id');
        $tituloId = DB::table('titulos')->where('abreviacion', 'ST')->value('id');

        $deportistas = [
            [
                'usuario_id' => $usuarioDeportistaId,
                'club_id' => $clubId,
                'categoria_id' => $categoriaId,
                'fecha_nacimiento' => $fecha_nacimiento,
                'genero_id' => $generoId,
                'nacionalidad_id' => $nacionalidadId,
                'elo_nacional' => 0,
                'elo_internacional' => 0,
                'titulo_id' => $tituloId,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('deportistas')->insert($deportistas);
    }
}
