<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;

class ClubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Obtener la liga creada
        $ligaId = DB::table('ligas')->where('nombre', 'Liga Ajedrez del Meta')->value('id');

        DB::table('clubes')->insert([
            [
                'liga_id' => $ligaId,
                'nombre' => 'Club de Ajedrez Villavicencio',
                'ubicacion' => 'Villavicencio, Meta',
                'presidente_id' => null,
                'contacto' => 'clubvillavicencio@ajedrezmeta.org',
                'logo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'liga_id' => $ligaId,
                'nombre' => 'Club Jaque Mate Meta',
                'ubicacion' => 'Villavicencio, Meta',
                'presidente_id' => null,
                'contacto' => 'jaquematemeta@gmail.com',
                'logo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'liga_id' => $ligaId,
                'nombre' => 'Club Peón de Oro',
                'ubicacion' => 'Villavicencio, Meta',
                'presidente_id' => null,
                'contacto' => 'peondeoro@ajedrezmeta.org',
                'logo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
