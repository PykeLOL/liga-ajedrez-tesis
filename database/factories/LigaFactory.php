<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LigaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nombre' => 'Liga Ajedrez del Meta',
            'descripcion' => 'La Liga de Ajedrez del Meta'
        ];
    }
}
