<?php

namespace App\Http\Resources\PlanEntrenamiento;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanEntrenamientoPreviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'fecha' => $this['fecha'],
            'dia' => $this['dia'],
            'hora_inicio' => $this['hora_inicio'],
            'hora_fin' => $this['hora_fin'],
        ];
    }
}
