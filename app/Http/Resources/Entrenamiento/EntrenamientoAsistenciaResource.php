<?php

namespace App\Http\Resources\Entrenamiento;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class EntrenamientoAsistenciaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'estado' => [
                'id' => $this->estado_entrenamiento_id,
                'nombre' => optional($this->estado)->nombre,
            ],

            'fecha' => $this->fecha,
            'hora_inicio' => Carbon::parse($this->hora_inicio)->format('H:i'),
            'hora_fin' => Carbon::parse($this->hora_fin)->format('H:i'),

            'deportistas' => $this->asistencias->map(function ($asistencia) {
                $deportista = $asistencia->deportista;

                return [
                    'id' => $deportista->id,
                    'nombre' => optional($deportista->usuario)->nombre_completo,
                    'numero_identificacion' => optional($deportista->usuario)->numero_identificacion,
                    'titulo' => optional($deportista->titulo)->abreviacion,
                    'categoria' => optional($deportista->categoria)->nombre,
                    'elo_maximo' => $deportista->elo_maximo,

                    'estado_asistencia' => [
                        'id' => $asistencia->estado_asistencia_id,
                        'nombre' => optional($asistencia->estado)->nombre,
                    ],

                    'hora_llegada' => $asistencia->hora_llegada ? Carbon::parse($asistencia->hora_llegada)->format('H:i') : null,
                    'observaciones' => $asistencia->observaciones,
                ];
            })->values(),
        ];
    }
}
