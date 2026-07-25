<?php

namespace App\Http\Resources\PlanEntrenamiento;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanEntrenamientoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,

            'nombre' => $this->nombre,

            'descripcion' => $this->descripcion,

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

            'horarios' => $this->horarios->map(function ($horario) {
                return [
                    'id' => $horario->id,
                    'dia_semana' => [
                        'id' => optional($horario->diaSemana)->id,
                        'nombre' => optional($horario->diaSemana)->nombre,
                    ],
                    'hora_inicio' => substr($horario->hora_inicio, 0, 5),
                    'hora_fin' => substr($horario->hora_fin, 0, 5),
                ];

            })->values(),

            'deportistas' => $this->deportistas->map(function ($deportista) {
                return [
                    'id' => $deportista->id,
                    'nombre' => optional($deportista->usuario)->nombre_completo,
                    'estado' => (bool) optional($deportista->pivot)->estado,
                ];

            })->values(),

            'tipo' => [
                'id' => optional($this->tipo)->id,
                'nombre' => optional($this->tipo)->nombre,
            ],

            'ubicacion' => $this->ubicacion,

            'url_mapa' => $this->url_mapa,

            'created_at' => optional($this->created_at)->format('Y-m-d H:i:s'),

            'updated_at' => optional($this->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
