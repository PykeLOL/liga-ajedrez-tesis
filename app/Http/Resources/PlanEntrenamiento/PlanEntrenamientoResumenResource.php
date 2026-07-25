<?php

namespace App\Http\Resources\PlanEntrenamiento;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanEntrenamientoResumenResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,

            'club' => $this->club ? [
                'id' => $this->club->id,
                'nombre' => $this->club->nombre,
            ] : null,

            'categoria' => $this->categoria ? [
                'id' => $this->categoria->id,
                'nombre' => $this->categoria->nombre,
            ] : null,

            'genero' => $this->genero ? [
                'id' => $this->genero->id,
                'nombre' => $this->genero->nombre,
            ] : null,

            'tipo' => $this->tipo ? [
                'id' => $this->tipo->id,
                'nombre' => $this->tipo->nombre,
            ] : null,

            'entrenador' => $this->entrenador ? [
                'id' => $this->entrenador->id,
                'nombre' => optional($this->entrenador->usuario)->nombre_completo,
                'numero_identificacion' => optional($this->entrenador->usuario)->numero_identificacion,
            ] : null,

            'evento' => $this->evento ? [
                'id' => $this->evento->id,
                'nombre' => optional($this->evento)->nombre,
                'tipo_evento' => optional($this->evento)->tipoEvento->slug,
            ] : null,

            'fecha_inicio' => Carbon::parse($this->fecha_inicio)->format('Y-m-d'),
            'fecha_fin' => Carbon::parse($this->fecha_fin)->format('Y-m-d'),
            'ubicacion' => $this->ubicacion,
            'url_mapa' => $this->url_mapa,

            'horarios' => $this->horarios->map(function ($horario) {
                return [
                    'id' => $horario->id,
                    'dia_semana' => optional($horario->diaSemana)->nombre,
                    'hora_inicio' => Carbon::parse($horario->hora_inicio)->format('g:i a'),
                    'hora_fin' => Carbon::parse($horario->hora_fin)->format('g:i a'),
                ];
            })->values(),

            'deportistas' => $this->deportistas->map(function ($deportista) {
                return [
                    'id' => $deportista->id,
                    'nombre' => optional($deportista->usuario)->nombre_completo,
                    'numero_identificacion' => optional($deportista->usuario)->numero_identificacion,
                    'elo_maximo' => $deportista->elo_maximo,
                    'titulo' => optional($deportista->titulo)->abreviacion,
                    'categoria' => optional($deportista->categoria)->nombre,
                ];
            })->values(),
        ];
    }
}
