<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RolFactory extends Factory
{
    public function definition($rol = null)
    {
        return [
            'nombre' => $rol ? $rol['nombre'] : 'Admin',
            'descripcion' => $rol ? $rol['descripcion'] : 'Rol administrador',
        ];
    }
}
