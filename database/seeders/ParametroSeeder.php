<?php

namespace Database\Seeders;

use App\Models\Parametro;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParametroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ligaId = DB::table('ligas')->where('nombre', 'Liga Ajedrez del Meta')->value('id');
        $parametros = [
            [
                'liga_id' => $ligaId,
                'nombre' => 'Titulo Principal Pagina',
                'valor' => 'Liga del Meta',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'liga_id' => $ligaId,
                'nombre' => 'Descripcion Footer Pagina',
                'valor' => 'Promoviendo el ajedrez como herramienta pedagógica y deportiva en todo el departamento del Meta.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'liga_id' => $ligaId,
                'nombre' => 'Contacto Ubicacion Url',
                'valor' => 'https://maps.app.goo.gl/DvBxLokkXCqyBQzK8',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'liga_id' => $ligaId,
                'nombre' => 'Contacto Correo Electronico',
                'valor' => 'contacto@ligameta.com',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'liga_id' => $ligaId,
                'nombre' => 'Contacto Telefono',
                'valor' => '3178711309',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'liga_id' => $ligaId,
                'nombre' => 'Red Social Facebook Url',
                'valor' => 'https://www.facebook.com/nelsonalexander.arangogallego',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'liga_id' => $ligaId,
                'nombre' => 'Red Social Instagram Url',
                'valor' => 'Liga de Ajedrez del Meta',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($parametros as $parametro) {
            Parametro::firstOrCreate(['nombre' => $parametro['nombre']], $parametro);
        }
    }
}
