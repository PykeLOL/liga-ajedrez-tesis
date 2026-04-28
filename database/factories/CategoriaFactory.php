<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoriaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nombre' => 'Mayores',
            'edad_minima' => 19,
            'edad_maxima' => 99,
            'descripcion' => 'Categoría para adultos.',
            'activo' => true,
        ];
    }
}
