<?php

namespace App\Http\Resources\PlanEntrenamiento;

use Carbon\Carbon;
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
            'fecha_fin'    => optional($this->fecha_fin)->format('Y-m-d'),

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

            'tipo' => [
                'id' => optional($this->tipo)->id,
                'nombre' => optional($this->tipo)->nombre,
            ],

            'evento' => [
                'id' => optional($this->evento)->id,
                'nombre' => optional($this->evento)->nombre,
            ],

            'ubicacion' => $this->ubicacion,
            'url_mapa'  => $this->url_mapa,

            'horarios' => $this->horarios->map(function ($horario) {
                return [
                    'dia_semana_id' => optional($horario->diaSemana)->id,
                    'hora_inicio' => Carbon::parse($horario->hora_inicio)->format('H:i'),
                    'hora_fin' => Carbon::parse($horario->hora_fin)->format('H:i'),
                ];
            })->values(),

            'deportistas' => $this->deportistas->map(function ($deportista) {
                return [
                    'id' => $deportista->id,
                    'nombre' => optional($deportista->usuario)->nombre_completo,
                    'numero_identificacion' => optional($deportista->usuario)->numero_identificacion,
                    'titulo' => optional($deportista->titulo)->abreviacion,
                    'categoria' => optional($deportista->categoria)->nombre,
                ];
            })->values(),
        ];
    }
}
