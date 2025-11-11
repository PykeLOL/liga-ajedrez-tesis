<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            TipoAccionSeeder::class,
            ModuloSeeder::class,
            PermisoSeeder::class,
            UsuarioSeeder::class,
            RolPermisoSeeder::class,
            LigaSeeder::class,
            ClubSeeder::class,
        ]);
    }
}
