<?php

namespace App\Http\Resources\PlanEntrenamiento;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanEntrenamientoIndexResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,

            'nombre' => $this->nombre,

            'fecha_inicio' => optional($this->fecha_inicio)->format('Y-m-d'),

            'fecha_fin' => optional($this->fecha_fin)->format('Y-m-d'),

            'club' => [
                'id' => optional($this->club)->id,
                'nombre' => optional($this->club)->nombre,
            ],

            'categoria' => [
                'id' => optional($this->categoria)->id,
                'nombre' => optional($this->categoria)->nombre,
            ],

            'genero' => [
                'id' => optional($this->genero)->id,
                'nombre' => optional($this->genero)->nombre,
            ],

            'entrenador' => [
                'id' => optional($this->entrenador)->id,
                'nombre' => optional(optional($this->entrenador)->usuario)->nombre_completo,
            ],

            'evento' => [
                'id' => optional($this->evento)->id,
                'nombre' => optional($this->evento)->nombre,
            ],

            'estado' => [
                'id' => optional($this->estado)->id,
                'nombre' => optional($this->estado)->nombre,
            ],

            'tipo' => [
                'id' => optional($this->tipo)->id,
                'nombre' => optional($this->tipo)->nombre,
            ],

            'ubicacion' => $this->ubicacion,
            'url_mapa' => $this->url_mapa,
        ];
    }
}
