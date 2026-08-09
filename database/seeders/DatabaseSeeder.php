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
            EstadoSeeder::class,
            EstadoEntrenamientoSeeder::class,
            EstadoPlanSeeder::class,
            EstadoAsistenciaSeeder::class,
            EstadoEventoSeeder::class,
            EstadoInscripcionSeeder::class,
            TipoIdentificacionSeeder::class,
            TipoEntrenamientoSeeder::class,
            TipoAccionSeeder::class,
            TipoEventoSeeder::class,
            RedesSocialesSeeder::class,
            RitmoSeeder::class,
            NacionalidadSeeder::class,
            RolSeeder::class,
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
            EventoSeeder::class,
            ParametroSeeder::class,
            ReaccionSeeder::class,
            PaisSeeder::class,
            DepartamentoSeeder::class,
            MunicipioSeeder::class,
            TipoNotificacionSeeder::class,
        ]);
    }
}
