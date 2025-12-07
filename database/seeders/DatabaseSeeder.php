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
            TipoIdentificacionSeeder::class,
            NacionalidadSeeder::class,
            RolSeeder::class,
            TipoAccionSeeder::class,
            ModuloSeeder::class,
            PermisoSeeder::class,
            UsuarioSeeder::class,
            RolPermisoSeeder::class,
            LigaSeeder::class,
            ClubSeeder::class,
            CategoriaSeeder::class,
            TituloSeeder::class,
            GeneroSeeder::class,
            EntidadCertificacionSeeder::class,
            EntrenadorSeeder::class,
            CertificacionEntrenadorSeeder::class,
            DeportistaSeeder::class,
            CategoriaEntrenadorSeeder::class,
            DiaSemanaSeeder::class,
            AperturaSeeder::class,
            RitmoSeeder::class,
        ]);
    }
}
