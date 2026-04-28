<?php

namespace Database\Factories;

use App\Models\Liga;
use App\Models\Estado;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClubFactory extends Factory
{
    public function definition()
    {
        return [
            'liga_id' => Liga::factory(),

            'nombre' => $this->faker->company(),
            'descripcion' => $this->faker->sentence(),
            'ubicacion' => $this->faker->city(),
            'direccion' => $this->faker->address(),
            'url_mapa' => $this->faker->url(),
            'presidente_id' => Usuario::factory(),
            'contacto' => $this->faker->safeEmail(),
            'logo' => 'clubes/logo/test.png',
            'estado_id' => Estado::factory(),
        ];
    }

    /**
     * Reusar liga existente
     */
    public function withLiga($ligaId)
    {
        return $this->state(fn () => [
            'liga_id' => $ligaId,
        ]);
    }

    /**
     * Reusar usuario como presidente
     */
    public function withPresidente($usuarioId)
    {
        return $this->state(fn () => [
            'presidente_id' => $usuarioId,
        ]);
    }
}
