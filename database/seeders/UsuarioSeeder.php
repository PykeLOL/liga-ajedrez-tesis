<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolAdminId = DB::table('roles')->where('nombre', 'Admin')->value('id');
        $rolPresidenteLigaId = DB::table('roles')->where('nombre', 'Presidente Liga')->value('id');
        $rolDeportistaId = DB::table('roles')->where('nombre', 'Deportista')->value('id');

        $users = [
            [
                'nombre' => 'admin',
                'apellido' => 'admin',
                'email' => 'admin@admin.com',
                'documento' => '123456789',
                'contraseña' => Hash::make('admin'),
                'rol_id' => $rolAdminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Nelson',
                'apellido' => 'Arango',
                'email' => 'xhd942@gmail.com',
                'documento' => '1121967543',
                'contraseña' => Hash::make('nelson123'),
                'rol_id' => $rolPresidenteLigaId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Brahian',
                'apellido' => 'Pulido',
                'email' => 'brahian@gmail.com',
                'documento' => '987654321',
                'contraseña' => Hash::make('brahian123'),
                'rol_id' => $rolDeportistaId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('usuarios')->insert($users);
    }
}
