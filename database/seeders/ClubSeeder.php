<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\ClubMedia;
use App\Models\RedSocial;
use App\Models\ClubRedSocial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClubSeeder extends Seeder
{
    public function run(): void
    {
        $ligaId = DB::table('ligas')
            ->where('nombre', 'Liga Ajedrez del Meta')
            ->value('id');

        $estadoId = DB::table('estados')
            ->where('nombre', 'Activo')
            ->value('id');

        $presidentes = [
            'Titan Chess' => DB::table('usuarios')
                ->where('email', 'martin.martinez@chess.com')
                ->value('id'),

            'Talentos Ajedrez' => DB::table('usuarios')
                ->where('email', 'javier.marroquin@chess.com')
                ->value('id'),

            'Magistral' => DB::table('usuarios')
                ->where('email', 'yobani.gonzalez@chess.com')
                ->value('id'),

            'Rey Dama' => DB::table('usuarios')
                ->where('email', 'guillermo.rey@chess.com')
                ->value('id'),
        ];

        $clubes = [
            [
                'liga_id' => $ligaId,
                'nombre' => 'Titan Chess',
                'descripcion' => 'Club de ajedrez con sede en Villavicencio, Meta, dedicado a promover el ajedrez y formar nuevos talentos de la región.',
                'ubicacion' => 'Villavicencio, Meta',
                'direccion' => 'Cra 30 #37-45, Barrio Barzal',
                'url_mapa' => 'https://maps.google.com/?q=Cra+30+%2337-45+Villavicencio+Meta',
                'presidente_id' => $presidentes['Titan Chess'],
                'contacto' => 'titan.chess@chess.com',
                'logo' => 'clubes/logo/1.png',
                'estado_id' => $estadoId,
            ],
            [
                'liga_id' => $ligaId,
                'nombre' => 'Talentos Ajedrez',
                'descripcion' => 'Club dedicado al desarrollo deportivo y formativo de jugadores de ajedrez en Villavicencio y el departamento del Meta.',
                'ubicacion' => 'Villavicencio, Meta',
                'direccion' => 'Calle 34 #24-18, Barrio El Buque',
                'url_mapa' => 'https://maps.google.com/?q=Calle+34+%2324-18+Villavicencio+Meta',
                'presidente_id' => $presidentes['Talentos Ajedrez'],
                'contacto' => 'talentos.ajedrez@chess.com',
                'logo' => 'clubes/logo/2.png',
                'estado_id' => $estadoId,
            ],
            [
                'liga_id' => $ligaId,
                'nombre' => 'Magistral',
                'descripcion' => 'Club de ajedrez orientado al entrenamiento competitivo y la formación de jugadores de diferentes categorías.',
                'ubicacion' => 'Villavicencio, Meta',
                'direccion' => 'Cra 39 #18-52, Barrio La Esperanza',
                'url_mapa' => 'https://maps.google.com/?q=Cra+39+%2318-52+Villavicencio+Meta',
                'presidente_id' => $presidentes['Magistral'],
                'contacto' => 'magistral@chess.com',
                'logo' => 'clubes/logo/3.png',
                'estado_id' => $estadoId,
            ],
            [
                'liga_id' => $ligaId,
                'nombre' => 'Rey Dama',
                'descripcion' => 'Club de ajedrez de Villavicencio dedicado a la práctica, enseñanza y promoción del ajedrez competitivo y recreativo.',
                'ubicacion' => 'Villavicencio, Meta',
                'direccion' => 'Calle 41 #31-27, Barrio Porfía',
                'url_mapa' => 'https://maps.google.com/?q=Calle+41+%2331-27+Villavicencio+Meta',
                'presidente_id' => $presidentes['Rey Dama'],
                'contacto' => 'rey.dama@chess.com',
                'logo' => 'clubes/logo/4.png',
                'estado_id' => $estadoId,
            ],
        ];

        foreach ($clubes as $clubData) {
            $club = Club::create($clubData);

            for ($i = 1; $i <= 5; $i++) {
                ClubMedia::create([
                    'club_id' => $club->id,
                    'tipo' => 'imagen',
                    'orden' => $i,
                    'path' => 'clubes/media/club_seeder/' . rand(1, 6) . '.jpg',
                    'descripcion' => 'Imagen ' . $i . ' del ' . $club->nombre,
                ]);
            }

            $redesSociales = RedSocial::all();

            foreach ($redesSociales as $index => $red) {
                ClubRedSocial::create([
                    'club_id' => $club->id,
                    'red_social_id' => $red->id,
                    'orden' => $index + 1,
                    'url' => 'https://www.' . $red->nombre . '.com/club_' . $club->id,
                ]);
            }
        }
    }
}
