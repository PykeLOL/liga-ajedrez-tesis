<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DeportistaSeeder extends Seeder
{
    public function run()
    {
        $rolDeportistaId = DB::table('roles')->where('nombre', 'Deportista')->value('id');
        $usuarioDeportistaId = DB::table('usuarios')
            ->where('rol_id', $rolDeportistaId)
            ->get();

        $generoId = DB::table('generos')->where('nombre', 'Masculino')->value('id');
        $nacionalidadId = DB::table('nacionalidades')->where('nombre', 'Colombia')->value('id');
        $clubId = DB::table('clubes')->where('nombre', 'Club Titan Chess')->value('id');

        $fechaNacimiento = '2001-10-15';
        $edad = Carbon::parse($fechaNacimiento)->age;

        $categoriaId = DB::table('categorias')
            ->where('nombre', '!=', 'Libre')
            ->where('edad_minima', '<=', $edad)
            ->where('edad_maxima', '>=', $edad)
            ->value('id');

        $this->crearDeportista(
            $usuarioDeportistaId[0]->id,
            144413246,
            $fechaNacimiento,
            $generoId,
            $nacionalidadId,
            $clubId,
            $categoriaId
        );

        $this->crearDeportista(
            $usuarioDeportistaId[1]->id,
            1503014,
            $fechaNacimiento,
            $generoId,
            $nacionalidadId,
            $clubId,
            $categoriaId
        );
    }

    private function crearDeportista(
        $usuarioId,
        $fideId,
        $fechaNacimiento,
        $generoId,
        $nacionalidadId,
        $clubId,
        $categoriaId
    ) {

        // $url = env('API_CHESSTOOLS_URL') . "/fide/player_info/?fide_id={$fideId}&history=true";
        // $response = Http::get($url);

        // if (!$response->successful()) {
        //     return;
        // }

        // $data = $response->json();

        // $history = $data['history'][0] ?? [];

        // $classical = $history['classical_rating'] ?? 0;
        // $rapid = $history['rapid_rating'] ?? 0;
        // $blitz = $history['blitz_rating'] ?? 0;

        $classical = 0;
        $rapid = 0;
        $blitz = 0;

        $eloMasAlto = max($classical, $rapid, $blitz);

        $tituloId = DB::table('titulos')
            ->where('abreviacion', 'ST')
            ->value('id');

        try {
            $url = env('API_CHESSTOOLS_URL') . "/fide/player_info/?fide_id={$fideId}&history=true";
            $response = Http::timeout(3)->get($url);
            if ($response->successful()) {
                $data = $response->json();
                $history = $data['history'][0] ?? [];

                $classical = $history['classical_rating'] ?? 0;
                $rapid = $history['rapid_rating'] ?? 0;
                $blitz = $history['blitz_rating'] ?? 0;

                $eloMasAlto = max($classical, $rapid, $blitz);

                $tituloId = DB::table('titulos')
                    ->where('nombre_fide', $data['fide_title'] ?? null)
                    ->value('id') ?? $tituloId;
            }
        } catch (\Throwable $e) {
            // La API está caída o no responde.
            // Se usan los valores por defecto.
        }

        DB::table('deportistas')->updateOrInsert(
            ['usuario_id' => $usuarioId],
            [
                'club_id' => $clubId,
                'categoria_id' => $categoriaId,
                'fecha_nacimiento' => $fechaNacimiento,
                'genero_id' => $generoId,
                'nacionalidad_id' => $nacionalidadId,
                'elo_nacional' => 0,
                'elo_internacional' => $eloMasAlto,
                'fide_id' => $fideId,
                'titulo_id' => $tituloId,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
