<?php

namespace Database\Factories;

use App\Models\Titulo;
use Illuminate\Database\Eloquent\Factories\Factory;

class TituloFactory extends Factory
{
    protected $model = Titulo::class;

    public function definition()
    {
        return [
            'nombre' => 'Sin título',
            'nombre_fide' => null,
            'abreviacion' => 'ST',
            'es_fide' => false
        ];
    }

    public function create($attributes = [], ?\Illuminate\Database\Eloquent\Model $parent = null)
    {
        $existing = Titulo::where('abreviacion', 'ST')->first();

        if ($existing) {
            return $existing;
        }

        return parent::create($attributes, $parent);
    }
}
