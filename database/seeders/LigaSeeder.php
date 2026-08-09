<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;

class LigaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $usuarioPresidente = DB::table('usuarios')->where('rol_id', DB::table('roles')->where('nombre', 'Presidente Liga')->value('id'))->first();
        DB::table('ligas')->insert([
            // 'id' => 1,
            'nombre' => 'Liga Ajedrez del Meta',
            'descripcion' => 'La Liga de Ajedrez del Meta promueve el desarrollo y la práctica del ajedrez en todo el departamento, fomentando la participación en torneos locales y nacionales.',
            'presidente_id' => $usuarioPresidente ? $usuarioPresidente->id : null,
            'logo' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
