<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TipoEventoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nombre' => 'Torneo',
            'abreviacion' => 'TOR',
            'descripcion' => 'Competencias formales de ajedrez.',
        ];
    }
}
