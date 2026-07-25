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

            'estado' => [
                'id' => $this->estado_entrenamiento_id,
                'nombre' => optional($this->estado)->nombre,
            ],

            'fecha' => $this->fecha,
            'hora_inicio' => Carbon::parse($this->hora_inicio)->format('H:i'),
            'hora_fin' => Carbon::parse($this->hora_fin)->format('H:i'),

            'ubicacion' => $this->ubicacion,
            'url_mapa' => $this->url_mapa,
            'google_event_id' => $this->google_event_id,

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
