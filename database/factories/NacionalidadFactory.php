<?php

namespace Database\Factories;

use App\Models\Nacionalidad;
use Illuminate\Database\Eloquent\Factories\Factory;

class NacionalidadFactory extends Factory
{
    protected $model = Nacionalidad::class;

    public function definition()
    {
        return [
            'nombre' => 'Colombia',
            'codigo' => 'COL'
        ];
    }

    public function create($attributes = [], ?\Illuminate\Database\Eloquent\Model $parent = null)
    {
        $existing = Nacionalidad::where('codigo', 'COL')->first();

        if ($existing) {
            return $existing;
        }

        return parent::create($attributes, $parent);
    }
}
