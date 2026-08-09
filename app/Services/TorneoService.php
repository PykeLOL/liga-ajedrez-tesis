<?php

namespace App\Services;

use App\Models\EventoInscripcion;
use App\Models\EstadoInscripcion;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TorneoService
{
    public function actualizarInscripcion(EventoInscripcion $inscripcion, array $data): EventoInscripcion
    {
        if (
            $inscripcion->estado_inscripcion_id === EstadoInscripcion::CANCELADO &&
            $data['estado_inscripcion_id'] !== EstadoInscripcion::CANCELADO
        ) {
            throw ValidationException::withMessages([
                'estado_inscripcion_id' => 'Una inscripción cancelada no puede modificarse.'
            ]);
        }

        return DB::transaction(function () use ($inscripcion, $data) {
            $inscripcion->estado_inscripcion_id = $data['estado_inscripcion_id'];
            $inscripcion->observacion = $data['observacion'] ?? null;
            $inscripcion->referencia_pago = $data['referencia_pago'] ?? null;
            $inscripcion->valor_pagado = $data['valor_pagado'] ?? null;

            if ($data['estado_inscripcion_id'] == EstadoInscripcion::PAGADO) {
                $inscripcion->pago = true;
            }

            if (in_array($data['estado_inscripcion_id'], [
                EstadoInscripcion::RECHAZADO,
                EstadoInscripcion::CANCELADO
            ])) {
                $inscripcion->pago = false;
            }

            $inscripcion->save();

            return $inscripcion->fresh([
                'estadoInscripcion',
                'deportista.usuario',
                'eventoCategoria.categoria',
                'eventoCategoria.ritmo'
            ]);
        });
    }
}
