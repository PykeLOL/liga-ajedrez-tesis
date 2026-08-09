<?php

namespace App\Http\Resources\Entrenamiento;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class EntrenamientoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'observaciones' => $this->observaciones,

            'plan_entrenamiento' => $this->planEntrenamiento
                ? [
                    'id' => $this->planEntrenamiento->id,
                    'nombre' => $this->planEntrenamiento->nombre,
                ]
                : null,

            'club' => [
                'id' => $this->club_id,
                'nombre' => optional($this->club)->nombre,
            ],

            'categoria' => [
                'id' => $this->categoria_id,
                'nombre' => optional($this->categoria)->nombre,
            ],

            'genero' => [
                'id' => $this->genero_id,
                'nombre' => optional($this->genero)->nombre,
            ],

            'entrenador' => [
                'id' => $this->entrenador_id,
                'nombre' => optional(optional($this->entrenador)->usuario)->nombre,
                'apellido' => optional(optional($this->entrenador)->usuario)->apellido,
            ],

            'tipo_entrenamiento' => [
                'id' => $this->tipo_entrenamiento_id,
                'nombre' => optional($this->tipo)->nombre,
            ],

            'estado' => [
                'id' => $this->estado_entrenamiento_id,
                'nombre' => optional($this->estado)->nombre,
            ],

            'evento' => [
                'id' => $this->evento_id,
                'nombre' => optional($this->evento)->nombre,
                'tipo_evento' => optional($this->evento)->tipoEvento->slug,
            ],

            'fecha' => $this->fecha,
            'hora_inicio' => Carbon::parse($this->hora_inicio)->format('H:i'),
            'hora_fin' => Carbon::parse($this->hora_fin)->format('H:i'),

            'ubicacion' => $this->ubicacion,
            'url_mapa' => $this->url_mapa,

            'deportistas' => $this->deportistas->map(function ($deportista) {
                return [
                    'id' => $deportista->id,
                    'nombre' => optional($deportista->usuario)->nombre_completo,
                    'numero_identificacion' => optional($deportista->usuario)->numero_identificacion,
                    'titulo' => optional($deportista->titulo)->abreviacion,
                    'categoria' => optional($deportista->categoria)->nombre,
                    'elo_maximo' => $deportista->elo_maximo,

                    'estado_asistencia_id' => $deportista->pivot->estado_asistencia_id ?? null,
                    'hora_llegada' => $deportista->pivot->hora_llegada ?? null,
                    'observaciones' => $deportista->pivot->observaciones ?? null,
                ];
            })->values(),
        ];
    }
}
