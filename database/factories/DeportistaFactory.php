<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Usuario;
use App\Models\Club;
use App\Models\Categoria;
use App\Models\Genero;
use App\Models\Nacionalidad;

class DeportistaFactory extends Factory
{
    public function definition()
    {
        return [
            'usuario_id' => Usuario::factory(),
            'club_id' => Club::factory(),
            'genero_id' => Genero::factory(),
            'nacionalidad_id' => Nacionalidad::factory()->create(),
            'titulo_id' => null,
            'fecha_nacimiento' => $this->faker->date('Y-m-d', '2005-01-01'),
            'elo_nacional' => $this->faker->numberBetween(1000, 2400),
            'elo_internacional' => $this->faker->numberBetween(1000, 2400),
            'fide_id' => $this->faker->unique()->numerify('######'),
            'categoria_id' => Categoria::factory(),
            'estado' => true,
        ];
    }

    public function withUsuario($usuarioId)
    {
        return $this->state(fn () => [
            'usuario_id' => $usuarioId,
        ]);
    }
}
