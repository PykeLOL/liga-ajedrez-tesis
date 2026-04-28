<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TipoIdentificacionFactory extends Factory
{
    public function definition()
    {
        return [
            'nombre' => 'Cédula de ciudadanía',
            'abreviacion' => 'CC',
        ];
    }
}
