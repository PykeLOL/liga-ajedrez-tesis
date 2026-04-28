<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\Rol;
use App\Models\TipoIdentificacion;

class UsuarioFactory extends Factory
{
    public function definition()
    {
        return [
            'nombre' => 'Test',
            'apellido' => 'User',
            'tipo_identificacion_id' => TipoIdentificacion::factory(),
            'numero_identificacion' => $this->faker->unique()->numerify('##########'),
            'email' => $this->faker->unique()->safeEmail(),
            'telefono' => $this->faker->phoneNumber(),
            'contraseña' => Hash::make('12345678'),
            'imagen_path' => null,
            'estado' => true,
            'rol_id' => Rol::factory(),
            'google_id' => null,
            'google_token' => null,
            'google_refresh' => null,
            'google_token_exp' => null,
        ];
    }

    /**
     * Estado para usar contraseña conocida en tests
     */
    public function withPassword(string $plain = '12345678')
    {
        return $this->state(fn () => [
            'contraseña' => Hash::make($plain),
        ]);
    }

    /**
     * Estado para forzar rol existente (evita crear otro)
     */
    public function withRol($rolId)
    {
        return $this->state(fn () => [
            'rol_id' => $rolId,
        ]);
    }

    /**
     * Estado para forzar tipo de identificación existente
     */
    public function withTipoIdentificacion($tipoId)
    {
        return $this->state(fn () => [
            'tipo_identificacion_id' => $tipoId,
        ]);
    }
}
