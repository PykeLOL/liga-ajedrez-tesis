<?php

namespace App\Http\Resources\Eventos;

use Illuminate\Http\Resources\Json\JsonResource;

class InscripcionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,

            'estado' => [
                'id' => $this->estadoInscripcion->id,
                'nombre' => $this->estadoInscripcion->nombre,
            ],

            'pago' => $this->pago,
            'comprobante' => !empty($this->comprobante_path),

            'mensaje' => $this->comprobante_path
                ? 'Tu inscripción fue registrada correctamente. El comprobante de pago será validado por la organización para confirmar tu participación.'
                : 'Tu inscripción fue registrada correctamente. Recuerda presentarte al menos una hora antes del inicio del torneo para realizar el pago y confirmar tu participación.'
        ];
    }
}
