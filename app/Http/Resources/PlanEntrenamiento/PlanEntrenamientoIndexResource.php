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
            'fecha_fin'    => optional($this->fecha_fin)->format('Y-m-d'),

            'horarios' => $this->horarios->map(function ($horario) {
                return [
                    'dia'          => optional($horario->diaSemana)->nombre,
                    'hora_inicio'  => $horario->hora_inicio,
                    'hora_fin'     => $horario->hora_fin,
                ];
            }),

            'club' => optional($this->club)->nombre,
            'categoria' => optional($this->categoria)->nombre,
            'genero' => optional($this->genero)->nombre,
            'entrenador' => optional(optional($this->entrenador)->usuario)->nombre_completo,
            'tipo_entrenamiento' => optional($this->tipo)->nombre,
            'evento' => optional($this->evento)->nombre,
            'estado' => optional($this->estado)->nombre,

            'total_deportistas' => $this->deportistas_count,

            'deportistas' => $this->deportistas->map(function ($deportista) {
                return [
                    'id' => $deportista->id,
                    'nombre' => optional($deportista->usuario)->nombre_completo,
                    'documento' => optional($deportista->usuario)->numero_identificacion,
                ];
            }),

            'ubicacion' => $this->ubicacion,
            'url_mapa' => $this->url_mapa,
        ];
    }
}
