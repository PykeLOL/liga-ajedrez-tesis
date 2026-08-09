<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DeportistaSeeder extends Seeder
{
    public function run(): void
    {
        $generoId = DB::table('generos')
            ->where('nombre', 'Masculino')
            ->value('id');

        $nacionalidadId = DB::table('nacionalidades')
            ->where('nombre', 'Colombia')
            ->value('id');

        $clubes = [
            DB::table('clubes')->where('nombre', 'Titan Chess')->value('id'),
            DB::table('clubes')->where('nombre', 'Talentos Ajedrez')->value('id'),
            DB::table('clubes')->where('nombre', 'Magistral')->value('id'),
            DB::table('clubes')->where('nombre', 'Rey Dama')->value('id'),
        ];

        $deportistas = [
            [
                'fide_id' => 1503014,
                'email' => 'magnus.carlsen@chess.com',
                'fecha_nacimiento' => '1990-11-30',
                'titulo' => 'GM',
            ],
            [
                'fide_id' => 2020009,
                'email' => 'fabiano.caruana@chess.com',
                'fecha_nacimiento' => '1992-07-30',
                'titulo' => 'GM',
            ],
            [
                'fide_id' => 2016192,
                'email' => 'hikaru.nakamura@chess.com',
                'fecha_nacimiento' => '1987-12-09',
                'titulo' => 'GM',
            ],
            [
                'fide_id' => 14205483,
                'email' => 'javokhir.sindarov@chess.com',
                'fecha_nacimiento' => '2005-12-08',
                'titulo' => 'GM',
            ],
            [
                'fide_id' => 12932970,
                'email' => 'vincent.keymer@chess.com',
                'fecha_nacimiento' => '2004-11-15',
                'titulo' => 'GM',
            ],
            [
                'fide_id' => 5202213,
                'email' => 'wesley.so@chess.com',
                'fecha_nacimiento' => '1993-10-09',
                'titulo' => 'GM',
            ],
            [
                'fide_id' => 24116068,
                'email' => 'anish.giri@chess.com',
                'fecha_nacimiento' => '1994-06-28',
                'titulo' => 'GM',
            ],
            [
                'fide_id' => 14204118,
                'email' => 'nodirbek.abdusattorov@chess.com',
                'fecha_nacimiento' => '2004-09-18',
                'titulo' => 'GM',
            ],
            [
                'fide_id' => 35009192,
                'email' => 'arjun.erigaisi@chess.com',
                'fecha_nacimiento' => '2003-09-03',
                'titulo' => 'GM',
            ],
            [
                'fide_id' => 12573981,
                'email' => 'alireza.firouzja@chess.com',
                'fecha_nacimiento' => '2003-06-18',
                'titulo' => 'GM',
            ],
            [
                'fide_id' => 20000197,
                'email' => 'faustino.oro@chess.com',
                'fecha_nacimiento' => '2013-10-14',
                'titulo' => 'MI',
            ],
            [
                'fide_id' => 4455665,
                'email' => 'santiago.lopez@chess.com',
                'fecha_nacimiento' => '2003-05-12',
                'titulo' => 'MI',
            ],
            [
                'fide_id' => 4444710,
                'email' => 'manuel.campos@chess.com',
                'fecha_nacimiento' => '1998-04-15',
                'titulo' => 'MI',
            ],
            [
                'fide_id' => 4403304,
                'email' => 'sebastian.sanchez@chess.com',
                'fecha_nacimiento' => '1999-08-20',
                'titulo' => 'MI',
            ],
            [
                'fide_id' => 4442024,
                'email' => 'esteban.valderrama@chess.com',
                'fecha_nacimiento' => '1993-01-10',
                'titulo' => 'MI',
            ],
            [
                'fide_id' => 4404017,
                'email' => 'jairo.hernandez@chess.com',
                'fecha_nacimiento' => '1990-06-14',
                'titulo' => 'MI',
            ],
            [
                'fide_id' => 4402308,
                'email' => 'cristian.hernandez@chess.com',
                'fecha_nacimiento' => '1997-03-25',
                'titulo' => 'MI',
            ],
            [
                'fide_id' => 3957624,
                'email' => 'angel.cordoba@chess.com',
                'fecha_nacimiento' => '2001-09-18',
                'titulo' => 'MI',
            ],
            [
                'fide_id' => 2039877,
                'email' => 'levy.rozman@chess.com',
                'fecha_nacimiento' => '1995-12-05',
                'titulo' => 'MI',
            ],
            [
                'fide_id' => 4430492,
                'email' => 'jose.cardoso@chess.com',
                'fecha_nacimiento' => '2004-08-11',
                'titulo' => 'GM',
            ],
            [
                'fide_id' => 4401190,
                'email' => 'jaime.cuartas@chess.com',
                'fecha_nacimiento' => '1975-08-15',
                'titulo' => 'GM',
            ],
            [
                'fide_id' => 4437128,
                'email' => 'santiago.avila@chess.com',
                'fecha_nacimiento' => '2004-08-18',
                'titulo' => 'GM',
            ],
            [
                'fide_id' => 4402669,
                'email' => 'andres.gallego@chess.com',
                'fecha_nacimiento' => '1988-12-19',
                'titulo' => 'GM',
            ],
            [
                'fide_id' => 3509265,
                'email' => 'roberto.garcia@chess.com',
                'fecha_nacimiento' => '1992-09-10',
                'titulo' => 'GM',
            ],
            [
                'fide_id' => 4401824,
                'email' => 'sergio.barrientos@chess.com',
                'fecha_nacimiento' => '1986-06-29',
                'titulo' => 'GM',
            ],
            [
                'fide_id' => 4400704,
                'email' => 'joshua.ruiz@chess.com',
                'fecha_nacimiento' => '1997-09-08',
                'titulo' => 'GM',
            ],
            [
                'fide_id' => 4400801,
                'email' => 'alder.escobar@chess.com',
                'fecha_nacimiento' => '1977-03-11',
                'titulo' => 'GM',
            ],
        ];

        foreach ($deportistas as $index => $data) {
            $usuarioId = DB::table('usuarios')
                ->where('email', $data['email'])
                ->value('id');

            if (!$usuarioId) {
                continue;
            }

            $fechaNacimiento = Carbon::parse($data['fecha_nacimiento']);
            $edad = $fechaNacimiento->age;

            $categoriaId = DB::table('categorias')
                ->where('nombre', '!=', 'Libre')
                ->where('edad_minima', '<=', $edad)
                ->where('edad_maxima', '>=', $edad)
                ->value('id');

            $clubId = $clubes[$index % count($clubes)];

            $this->crearDeportista(
                $usuarioId,
                $data['fide_id'],
                $data['fecha_nacimiento'],
                $generoId,
                $nacionalidadId,
                $clubId,
                $categoriaId,
                $data['titulo']
            );

            DB::table('usuarios')
                ->where('id', $usuarioId)
                ->update([
                    'imagen_path' => 'usuarios/' . $data['fide_id'] . '.jpg',
                    'updated_at' => now(),
                ]);
        }
    }

    private function crearDeportista(
        int $usuarioId,
        int $fideId,
        string $fechaNacimiento,
        int $generoId,
        int $nacionalidadId,
        ?int $clubId,
        ?int $categoriaId,
        ?string $tituloAbreviacion
    ): void {
        $eloMasAlto = 0;

        $tituloId = DB::table('titulos')
            ->where('abreviacion', $tituloAbreviacion)
            ->value('id');

        try {
            $url = env('API_CHESSTOOLS_URL') . "/fide/player/{$fideId}";
            $response = Http::timeout(3)->get($url);

            if ($response->successful()) {
                $data = $response->json();

                $standard = $data['standard'] ?? 0;
                $rapid = $data['rapid'] ?? 0;
                $blitz = $data['blitz'] ?? 0;

                $eloMasAlto = max($standard, $rapid, $blitz);

                $tituloId = DB::table('titulos')
                    ->where('abreviacion', $data['title'] ?? null)
                    ->value('id') ?? $tituloId;
            }
        } catch (\Throwable $e) {
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
