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
            ->where('descripcion', 'Activo')
            ->value('id');

        $adminId = DB::table('usuarios')
            ->where('email', 'admin@admin.com')
            ->value('id');

        $clubes = [
            [
                'liga_id' => $ligaId,
                'nombre' => 'Club Titan Chess',
                'descripcion' => 'Club de ajedrez con sede en Villavicencio, Meta, dedicado a promover el ajedrez en la región y formar nuevos talentos.',
                'ubicacion' => 'Villavicencio, Meta',
                'direccion' => 'Cra 30 #37-45, Barrio Barzal',
                'url_mapa' => 'https://maps.google.com/?q=Cra+30+%2337-45+Villavicencio+Meta',
                'presidente_id' => $adminId,
                'contacto' => 'clubtitanchess@ajedrezmeta.org',
                'logo' => 'clubes/logo/1.png',
                'estado_id' => $estadoId,
            ],
            [
                'liga_id' => $ligaId,
                'nombre' => 'Club Jaque Mate Meta',
                'descripcion' => 'Club de ajedrez con sede en Villavicencio, Meta, dedicado a promover el ajedrez en la región y formar nuevos talentos.',
                'ubicacion' => 'Villavicencio, Meta',
                'direccion' => 'Av 40 #15-62, Barrio La Esperanza',
                'url_mapa' => 'https://maps.google.com/?q=Av+40+%2315-62+Villavicencio+Meta',
                'presidente_id' => $adminId,
                'contacto' => 'jaquematemeta@gmail.com',
                'logo' => 'clubes/logo/2.png',
                'estado_id' => $estadoId,
            ],
            [
                'liga_id' => $ligaId,
                'nombre' => 'Club Peón de Oro',
                'descripcion' => 'Club de ajedrez con sede en Villavicencio, Meta, dedicado a promover el ajedrez en la región y formar nuevos talentos.',
                'ubicacion' => 'Villavicencio, Meta',
                'direccion' => 'Calle 38 #29-18, Barrio La Grama',
                'url_mapa' => 'https://maps.google.com/?q=Calle+38+%2329-18+Villavicencio+Meta',
                'presidente_id' => $adminId,
                'contacto' => 'peondeoro@ajedrezmeta.org',
                'logo' => 'clubes/logo/3.png',
                'estado_id' => $estadoId,
            ],
            [
                'liga_id' => $ligaId,
                'nombre' => 'Club Chess Pro Max',
                'descripcion' => 'Club de ajedrez con sede en Villavicencio, Meta, dedicado a promover el ajedrez en la región y formar nuevos talentos.',
                'ubicacion' => 'Villavicencio, Meta',
                'direccion' => 'Calle 20 #37-1148, Barrio La Esperanza',
                'url_mapa' => 'https://maps.google.com/?q=Calle+20+%2337-1148+Villavicencio+Meta',
                'presidente_id' => $adminId,
                'contacto' => 'chesspromax@ajedrezmeta.org',
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
