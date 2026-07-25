<?php

namespace App\Http\Resources\Entrenamiento;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class EntrenamientoIndexResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,

            'club' => optional($this->club)->nombre,
            'categoria' => optional($this->categoria)->nombre,
            'genero' => optional($this->genero)->nombre,
            'tipo_entrenamiento' => optional($this->tipo)->nombre,
            'estado' => optional($this->estado)->nombre,

            'entrenador' => trim(
                optional(optional($this->entrenador)->usuario)->nombre . ' ' .
                optional(optional($this->entrenador)->usuario)->apellido
            ),

            'fecha' => $this->fecha,
            'hora_inicio' => Carbon::parse($this->hora_inicio)->format('g:i a'),
            'hora_fin' => Carbon::parse($this->hora_fin)->format('g:i a'),

            'ubicacion' => $this->ubicacion,
            'url_mapa' => $this->url_mapa,

            'evento' => optional($this->evento)->nombre,
            'plan' => optional($this->planEntrenamiento)->nombre,

            'total_deportistas' => $this->deportistas->count(),
        ];
    }
}
