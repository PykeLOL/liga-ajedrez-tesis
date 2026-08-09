<?php

namespace App\Http\Resources\Notificaciones;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificacionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,

            'tipo' => [
                'id' => $this->tipoNotificacion->id,
                'nombre' => $this->tipoNotificacion->nombre,
                'icono' => $this->tipoNotificacion->icono,
            ],

            'modulo' => $this->modulo ? [
                'id' => $this->modulo->id,
                'nombre' => ucwords(str_replace('_', ' ', $this->modulo->nombre)),
            ] : null,

            'url' => $this->url,
            'leida' => $this->leida,
            'fecha_lectura' => $this->fecha_lectura,
            'created_at' => $this->created_at,
        ];
    }
}
