<?php

namespace App\Http\Resources\PlanEntrenamiento;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanEntrenamientoGeneracionPreviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'ya_generados' => $this['ya_generados'],
            'total_existentes' => $this['total_existentes'],
            'total_deportistas' => $this['total_deportistas'],
            'total_entrenamientos' => count($this['entrenamientos']),
            'entrenamientos' => $this['entrenamientos'],
            'deportistas' => $this['deportistas'],
        ];
    }
}
